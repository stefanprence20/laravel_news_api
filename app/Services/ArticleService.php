<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Author;
use App\Models\Source;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ArticleService
{

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $perPage = $request->input('per_page', 10);

        return Article::with(['authors', 'source'])->paginate($perPage);
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
}
