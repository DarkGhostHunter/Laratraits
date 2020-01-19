<?php

namespace DarkGhostHunter\Laratraits\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\Repository;
use DarkGhostHunter\Laratraits\Controllers\CacheKeysRequest;

/**
 * Class CacheStaticResponse
 * ---
 * Caches a (hopefully static) Response for the given minutes, and other store apart of the default.
 *
 * @package DarkGhostHunter\Laratraits\Middleware
 */
class CacheStaticResponse
{
    use CacheKeysRequest;

    /**
     * Cache manager
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * Create a new ValidateSignature instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     */
    public function __construct(Repository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $ttl  Minutes to hold the response in the cache
     * @param  string|null  $store
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle($request, Closure $next, int $ttl = 1, string $store = null)
    {
        if ($response = $this->hasResponseInCache($request, $store)) {
            return $response;
        }

        return $this->cacheResponse($request, $next($request), $ttl * 60, $store);
    }

    /**
     * Puts the Response in the cache.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @param  string|null  $store
     * @return \Illuminate\Http\Response
     */
    public function cacheResponse($request, $response, $ttl, string $store = null)
    {
        $this->cache->store($store)->put($this->requestCacheKey($request), $response, $ttl);

        return $response;
    }

    /**
     * Returns the response if it exists in the cache
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $store
     * @return \Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function hasResponseInCache(Request $request, string $store = null)
    {
        return $this->cache->store($store)->get($this->requestCacheKey($request));
    }
}
