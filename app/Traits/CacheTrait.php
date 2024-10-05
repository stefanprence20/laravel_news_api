<?php

namespace App\Traits;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    /**
     * Cache any kind of results.
     *
     * @param string $baseKey
     * @param array $requestParams
     * @param Closure $callback
     * @param string|null $tag
     * @param int|null $ttl
     * @return mixed
     */
    public function cacheResults(string $baseKey, array $requestParams, Closure $callback, ?string $tag = null, ?int $ttl = null)
    {
        $ttl = $ttl ?? env('CACHE_TTL', 3600);

        $cacheKey = $baseKey . md5(json_encode($requestParams));

        return $tag
            ? Cache::tags([$tag])->remember($cacheKey, $ttl, $callback)
            : Cache::remember($cacheKey, $ttl, $callback);
    }
}
