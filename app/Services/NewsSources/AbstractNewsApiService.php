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

    // Generic API request method

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
        // Always attach the API key to the request parameters
        $params['apiKey'] = $this->apiKey;

        // Set default headers
        $defaultHeaders = [
            'Accept' => 'application/json',
        ];

        // Merge default headers with any additional headers
        $headers = array_merge($defaultHeaders, $headers);

        // Perform the request using the method provided
        $response = Http::withHeaders($headers)->$method($this->url . $endpoint, $params);

        // Check for a successful response
        if (!$response->successful()) {
            throw new Exception('API request error: ' . $response->body());
        }

        // Return the response JSON
        return $response->json();
    }
}
