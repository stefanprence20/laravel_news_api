<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsServiceInterface;
use App\Models\Source;
use Illuminate\Support\Facades\Http;

class NYTimesApiService implements NewsServiceInterface
{

    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function fetchArticles(): array
    {
        $response = Http::get('https://api.nytimes.com/svc/mostpopular/v2/viewed/1.json', [
            'api-key' => $this->apiKey,
        ]);

        $articles = [];
        foreach ($response->json()['results'] as $article) {
            $articles[] = [
                'title' => $article['title'],
                'content' => $article['abstract'],
                'author' => $this->extractAuthorsFromByline($article['byline']),
                'url' => $article['url'],
                'published_at' => $article['published_date'],
                'source' => $article['source'] ?? Source::NYTIMES_SOURCE_NAME,
            ];
        }

        return $articles;
    }

    private function extractAuthorsFromByline(string $byline): array
    {
        $byline = preg_replace('/^By\s+/i', '', $byline);
        $byline = str_replace(' and ', ',', $byline);

        $authors = explode(',', $byline);

        return array_map('trim', $authors);
    }
}
