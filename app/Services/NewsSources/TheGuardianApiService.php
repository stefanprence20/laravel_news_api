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

        $articles = [];

        foreach ($response['response']['results'] as $article) {
            $articles[] = [
                'title' => $article['webTitle'],
                'content' => $this->getContentSummary($article['blocks'], $article['fields']),
                'author' => $this->extractAuthorsFromTags($article['tags']),
                'url' => $article['webUrl'],
                'published_at' => $article['webPublicationDate'],
                'source' => Source::THE_GUARDIAN_SOURCE_NAME,
            ];
        }

        return $articles;
    }

    private function extractAuthorsFromTags(array $tags): array
    {
        $authors = [];

        foreach ($tags as $tag) {
            $authors[] = $tag['webTitle'];
        }

        return $authors;
    }

    private function getContentSummary(array $blocks, array $fields): ?string
    {
        if (isset($blocks['requestedBodyBlocks']['body:key-events'][0])) {
            return $blocks['requestedBodyBlocks']['body:key-events'][0]['bodyTextSummary'] ?? '';
        } elseif (isset($fields['trailText'])) {
            return $fields['trailText'];
        }

        return '';
    }
}
