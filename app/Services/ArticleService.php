<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleService
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function paginate(Request $request): mixed
    {
        $perPage = $request->input('per_page', 10);

        return Article::paginate($perPage);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request): mixed
    {
        $perPage = $request->input('per_page', 10);

        return Article::paginate($perPage);
    }

    /**
     * @param mixed $articleData
     * @return void
     */
    public function save(mixed $articleData): void
    {
        Article::updateOrCreate(
            ['url' => $articleData['url']],
            [
                'title' => $articleData['title'],
                'content' => $articleData['content'] ?? '',
                'author' => $articleData['author'] ?? '',
                'published_at' => $articleData['publishedAt'] ?? now(),
                'source' => $articleData['source']['name'] ?? 'Unknown'
            ]
        );
    }
}
