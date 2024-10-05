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

        return $this->extractArticles($response['results']);
    }

    protected function extractTitle(array $article): string
    {
        return $article['title'];
    }

    protected function extractContent(array $article): string
    {
        return $article['abstract'];
    }

    protected function extractAuthors(array $article): array
    {
        $byline = $article['byline'];
        $byline = preg_replace('/^By\s+/i', '', $byline);
        $byline = str_replace(' and ', ',', $byline);

        $authors = explode(',', $byline);

        return array_map('trim', $authors);
    }

    protected function extractUrl(array $article): string
    {
        return $article['url'];
    }

    protected function extractPublishedAt(array $article): string
    {
        return $article['published_date'];
    }

    protected function extractSource(array $article): string
    {
        return $article['source'] ?? Source::NYTIMES_SOURCE_NAME;
    }
}
