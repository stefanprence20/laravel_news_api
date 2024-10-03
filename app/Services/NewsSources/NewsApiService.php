<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsServiceInterface;
use Illuminate\Support\Facades\Http;

class NewsApiService implements NewsServiceInterface
{

    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function fetchDailyArticles(): array
    {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => $this->apiKey,
            'country' => 'us',
        ]);

        $articles = [];

        foreach ($response->json()['articles'] as $article) {
            $articles[] = [
                'title' => $article['title'],
                'content' => $article['content'],
                'author' => $article['author'],
                'url' => $article['url'],
                'published_at' => $article['publishedAt'],
                'source' => $article['source']['name'],
            ];
        }

        return $articles;
    }
}
