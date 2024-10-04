<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Author;
use App\Models\Source;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
     * @param mixed $articleData
     * @return void
     */
    public function save(array $articleData): void
    {
        $source = Source::updateOrCreate(
            ['name' => $articleData['source'] ?? 'Unknown']
        );

        $article = Article::updateOrCreate(
            ['url' => $articleData['url']],
            [
                'title' => $articleData['title'],
                'content' => $articleData['content'] ?? '',
                'published_at' => $articleData['publishedAt'] ?? now(),
                'source_id' => $source->id,
            ]
        );

        if (isset($articleData['author'])) {
            $authorsInput = is_array($articleData['author'])
                ? $articleData['author']
                : explode(',', $articleData['author']);

            $authorIds = [];
            foreach ($authorsInput as $authorName) {
                $authorName = trim($authorName);
                if (!empty($authorName)) {
                    $author = Author::firstOrCreate(['name' => $authorName]);
                    $authorIds[] = $author->id;
                }
            }

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
