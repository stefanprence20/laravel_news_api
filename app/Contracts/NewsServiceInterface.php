<?php
namespace App\Contracts;

interface NewsServiceInterface
{
    const METHOD_GET = 'get';

    const METHOD_POST = 'post';

    const METHOD_PUT = 'put';

    const METHOD_DELETE = 'delete';

    const METHOD_PATCH = 'patch';

    public function fetchArticles(): array;

    public function makeRequest(string $method, string $endpoint, array $params = [], array $headers = []): array;
}

