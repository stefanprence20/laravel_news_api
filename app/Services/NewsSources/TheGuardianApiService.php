<?php

namespace App\Services\NewsSources;

use App\Contracts\NewsServiceInterface;
use App\Models\Source;
use Illuminate\Support\Facades\Http;

class TheGuardianApiService implements NewsServiceInterface
{

    protected string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function fetchArticles(): array
    {
        $response = Http::get('https://content.guardianapis.com/search', [
            'api-key' => $this->apiKey,
            'page-size' => 20,
            'show-tags' => 'contributor',
            'show-fields' => 'trailText',
            'show-blocks' => 'body:key-events',
        ]);

        $articles = [];

        foreach ($response->json()['response']['results'] as $article) {
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
