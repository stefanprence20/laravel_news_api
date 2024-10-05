<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Author;
use App\Models\Source;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArticleService
{

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $page = $request->get('page', 1);
        $perPage = $request->input('per_page', 10);

        return Cache::tags(['articles'])->remember("articles_index_page_{$page}", 60, function () use ($perPage) {
            return Article::with(['authors', 'source'])->paginate($perPage);
        });
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function search(Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);

        $query = Article::query();

        if ($keyword = $request->input('keyword')) {
            $query->where('title', 'LIKE', '%' . $keyword . '%');
        }

        if ($fromDate = $request->input('from_date')) {
            $query->where('published_at', '>=', $fromDate);
        }

        if ($toDate = $request->input('to_date')) {
            $query->where('published_at', '<=', $toDate);
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

        return $query->with(['authors', 'source'])->paginate($perPage);
    }

    /**
     * @param array $article
     * @return void
     */
    public function save(array $article): void
    {
        // Using a transaction to ensure all data is saved or none is saved
        DB::transaction(function () use ($article) {
            // Step 1: Update or create the source
            $source = $this->saveSource($article);

            // Step 2: Update or create the article
            $articleModel = $this->saveArticle($article, $source);

            // Step 3: Update or create the authors and associate with the article
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
                // Using firstOrCreate for each author
                $author = Author::firstOrCreate(['name' => $authorName]);
                $authorIds[] = $author->id;
            }
        }

        if (!empty($authorIds)) {
            // Sync authors without detaching existing relationships
            $article->authors()->syncWithoutDetaching($authorIds);
        }
    }

    /**
     * @param $article
     * @return mixed
     */
    public function show($article): mixed
    {
        return Cache::tags(['articles'])->remember("articles_show_page_{$article->id}", 60, function () use ($article) {
            return $article->load(['authors', 'source']);
        });
    }
}
