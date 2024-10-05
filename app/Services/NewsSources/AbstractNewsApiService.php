<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsServiceInterface;
use Exception;
use Illuminate\Support\Facades\Http;

abstract class AbstractNewsApiService implements NewsServiceInterface
{
    /**
     * @var string $apiKey
     */
    protected string $apiKey;

    /**
     * @var string $url
     */
    protected string $url;

    /**
     * @param string $apiKey
     * @param string $url
     */
    public function __construct(string $apiKey, string $url)
    {
        $this->apiKey = $apiKey;
        $this->url = $url;
    }

    abstract protected function extractTitle(array $article): string;

    abstract protected function extractContent(array $article): string;

    abstract protected function extractAuthors(array $article): mixed;

    abstract protected function extractUrl(array $article): string;

    abstract protected function extractPublishedAt(array $article): string;

    abstract protected function extractSource(array $article): string;

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return array
     * @throws Exception
     */
    public function makeRequest(string $method, string $endpoint, array $params = [], array $headers = []): array
    {
        $defaultHeaders = [
            'Accept' => 'application/json',
        ];

        $headers = array_merge($defaultHeaders, $headers);

        $response = Http::withHeaders($headers)->$method($this->url . $endpoint, $params);

        // Check for a successful response
        if (!$response->successful()) {
            throw new Exception('API request error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * @param array $articlesData
     * @return array
     */
    public function extractArticles(array $articlesData): array
    {
        $articles = [];

        foreach ($articlesData as $article) {
            $articles[] = [
                'title' => $this->extractTitle($article),
                'content' => $this->extractContent($article),
                'author' => $this->extractAuthors($article),
                'url' => $this->extractUrl($article),
                'published_at' => $this->extractPublishedAt($article),
                'source' => $this->extractSource($article),
            ];
        }

        return $articles;
    }
}
