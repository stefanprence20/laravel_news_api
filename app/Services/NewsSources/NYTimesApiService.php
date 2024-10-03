<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsServiceInterface;
use Illuminate\Support\Facades\Http;

class NYTimesApiService implements NewsServiceInterface
{

    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function fetchDailyArticles(): array
    {
        $response = Http::get('https://api.nytimes.com/svc/topstories/v2/home.json', [
            'api-key' => $this->apiKey,
        ]);

        $articles = [];
        foreach ($response->json()['results'] as $article) {
            $articles[] = [
                'title' => $article['title'],
                'content' => $article['abstract'],
                'author' => $article['byline'],
                'url' => $article['url'],
                'published_at' => $article['published_date'],
                'source' => $article['source'] ?? 'New York Times',
            ];
        }

        return $articles;
    }
}
