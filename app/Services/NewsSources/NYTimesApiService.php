<?php

namespace App\Services\NewsSources;

use App\Models\Source;
use Exception;

class NYTimesApiService extends AbstractNewsApiService
{
    public function __construct()
    {
        $this->apiKey = config('news_services.nytimes_api_key');
        $this->url = config('news_services.nytimes_api_url');
        parent::__construct($this->apiKey, $this->url);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchArticles(): array
    {
        $response = $this->makeRequest(self::METHOD_GET, '/svc/mostpopular/v2/viewed/1.json');

        $articles = [];
        foreach ($response['results'] as $article) {
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
