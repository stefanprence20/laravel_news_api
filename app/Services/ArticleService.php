<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Author;
use App\Models\Source;
use App\Traits\CacheTrait;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ArticleService
{
    use CacheTrait;

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);

        return $this->cacheResults(
            'articles_index_',
            $request->all(),
            fn() => Article::with(['authors', 'source'])->paginate($perPage),
            'articles'
        );
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function search(Request $request): LengthAwarePaginator
    {
        $request->validate([
            'keyword'     => 'nullable|string|max:255',
            'per_page'    => 'nullable|integer',
            'from_date'   => 'nullable|date|before_or_equal:today',
            'to_date'     => 'nullable|date|after_or_equal:from_date|before_or_equal:today',
            'source'      => 'nullable|string|max:255',
            'author'      => 'nullable|string|max:255',
        ]);

        $perPage = $request->input('per_page', 10);

        $query = Article::query();

        if ($keyword = $request->input('keyword')) {
            $query->where('title', 'LIKE', '%' . $keyword . '%');
        }

        if ($fromDate = $request->input('from_date')) {
            $query->where('published_at', '>=', Carbon::parse($fromDate)->startOfDay());
        }

        if ($toDate = $request->input('to_date')) {
            $query->where('published_at', '<=', Carbon::parse($toDate)->endOfDay());
        }

        if ($sourceName = $request->input('source')) {
            $query->whereHas('source', function ($q) use ($sourceName) {
                $q->where('name', 'like', '%' . $sourceName . '%');
            });
        }

        if ($authorName = $request->input('author')) {
            $query->whereHas('authors', function ($q) use ($authorName) {
                $q->where('name', 'like', '%' . $authorName . '%');
            });
        }

        return $this->cacheResults(
            'articles_search_',
            $request->all(),
            fn() => $query->with(['authors', 'source'])->paginate($perPage),
            'articles'
        );
    }

    /**
     * @param array $article
     * @return void
     */
    public function save(array $article): void
    {
        // Using a transaction to ensure all data is saved or none is saved
        DB::transaction(function () use ($article) {
            $source = $this->saveSource($article);

            $articleModel = $this->saveArticle($article, $source);

            if (isset($article['author'])) {
                $this->saveAuthors($articleModel, $article['author']);
            }
        });
    }

    /**
     * @param array $article
     * @return Source
     */
    protected function saveSource(array $article): Source
    {
        return Source::updateOrCreate(
            ['name' => Arr::get($article, 'source', 'Unknown')]
        );
    }

    /**
     * @param array $article
     * @param Source $source
     * @return Article
     */
    protected function saveArticle(array $article, Source $source): Article
    {
        return Article::updateOrCreate(
            ['url' => $article['url']],
            [
                'title' => $article['title'],
                'content' => Arr::get($article, 'content', ''),
                'published_at' => Arr::get($article, 'publishedAt', now()),
                'source_id' => $source->id,
            ]
        );
    }

    /**
     * @param Article $article
     * @param $authors
     * @return void
     */
    protected function saveAuthors(Article $article, $authors): void
    {
        $authorsInput = is_array($authors) ? $authors : explode(',', $authors);
        $authorIds = [];

        foreach ($authorsInput as $authorName) {
            $authorName = trim($authorName);
            if (!empty($authorName)) {
                $author = Author::firstOrCreate(['name' => $authorName]);
                $authorIds[] = $author->id;
            }
        }

        if (!empty($authorIds)) {
            $article->authors()->syncWithoutDetaching($authorIds);
        }
    }

    /**
     * @param $article
     * @return mixed
     */
    public function show($article): mixed
    {
        return $this->cacheResults(
            'articles_show_',
            [$article->id],
            fn() => $article->load(['authors', 'source']),
            'articles'
        );
    }
}
