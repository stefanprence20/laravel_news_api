<?php

namespace App\Services\NewsSources;

use App\Models\Source;
use Exception;

class TheGuardianApiService extends AbstractNewsApiService
{
    public function __construct()
    {
        $this->apiKey = config('news_services.guardian_api_key');
        $this->url = config('news_services.guardian_api_url');
        parent::__construct($this->apiKey, $this->url);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function fetchArticles(): array
    {
        $response = $this->makeRequest(self::METHOD_GET, '/search', [
            'page-size' => 20,
            'show-tags' => 'contributor',
            'show-fields' => 'trailText',
            'show-blocks' => 'body:key-events',
        ]);

        return $this->extractArticles($response['response']['results']);
    }

    protected function extractTitle(array $article): string
    {
        return $article['webTitle'];
    }

    protected function extractContent(array $article): string
    {
        $blocks = $article['blocks'];
        $fields = $article['fields'];
        if (isset($blocks['requestedBodyBlocks']['body:key-events'][0])) {
            return $blocks['requestedBodyBlocks']['body:key-events'][0]['bodyTextSummary'] ?? '';
        } elseif (isset($fields['trailText'])) {
            return $fields['trailText'];
        }
        return '';
    }

    protected function extractAuthors(array $article): array
    {
        $authors = [];
        $tags = $article['tags'] ?? [];
        foreach ($tags as $tag) {
            $authors[] = $tag['webTitle'];
        }
        return $authors;
    }

    protected function extractUrl(array $article): string
    {
        return $article['webUrl'];
    }

    protected function extractPublishedAt(array $article): string
    {
        return $article['webPublicationDate'];
    }

    protected function extractSource(array $article): string
    {
        return Source::THE_GUARDIAN_SOURCE_NAME;
    }
}
