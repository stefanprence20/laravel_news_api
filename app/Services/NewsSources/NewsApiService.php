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

        $articles = [];

        foreach ($response['articles'] as $article) {
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
