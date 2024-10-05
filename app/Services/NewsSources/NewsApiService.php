<?php

namespace App\Services\NewsSources;

use Exception;

class NewsApiService extends AbstractNewsApiService
{
    public function __construct()
    {
        $this->apiKey = config('news_services.news_api_key');
        $this->url = config('news_services.news_api_url');
        parent::__construct($this->apiKey, $this->url);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchArticles(): array
    {
        $response = $this->makeRequest(self::METHOD_GET, '/top-headlines', [
            'country' => 'us',
        ]);

        return $this->extractArticles($response['articles']);
    }

    protected function extractTitle(array $article): string
    {
        return $article['title'];
    }

    protected function extractContent(array $article): string
    {
        return $article['content'];
    }

    protected function extractAuthors(array $article): array
    {
        return $article['author'];
    }

    protected function extractUrl(array $article): string
    {
        return $article['url'];
    }

    protected function extractPublishedAt(array $article): string
    {
        return $article['publishedAt'];
    }

    protected function extractSource(array $article): string
    {
        return $article['source']['name'];
    }
}
