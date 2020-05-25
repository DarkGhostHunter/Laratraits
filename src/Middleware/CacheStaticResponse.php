<?php
/**
 * CacheStaticResponse
 *
 * Caches a (hopefully static) Response for the given minutes, and other store apart of the default.
 *
 *     Route::get('post', 'PostController@show')
 *          ->middleware('DarkGhostHunter\Laratraits\Middleware\CacheStaticResponse');
 *
 * Alternatively, you can register an alias in your HTTP Kernel for this middleware, making it easy
 * to add options like the time-to-live for the cached response, and which cache store to use.
 *
 *     Route::get('post', 'PostController@show')
 *          ->middleware('cache.static:1440,redis');
 *
 * This caches the request for the requester fingerprint (including the IP). If you need to cache
 * the response for all requests, you should manage that behaviour in your controller instead.
 *
 * ---
 * MIT License
 *
 * Copyright (c) Italo Israel Baeza Cabrera
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Contracts\Cache\Factory;

class CacheStaticResponse
{
    /**
     * Cache manager instance.
     *
     * @var \Illuminate\Cache\CacheManager
     */
    protected $cache;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Cache\Factory  $cache
     */
    public function __construct(Factory $cache)
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
        $this->cache->store($store)->put($this->cacheKey($request), $response, $ttl);

        return $response;
    }

    /**
     * Check if the response exists in the cache
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $store
     * @return \Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function hasResponseInCache(Request $request, string $store = null)
    {
        return $this->cache->store($store)->get($this->cacheKey($request));
    }

    /**
     * Returns the key to use for caching the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function cacheKey(Request $request)
    {
        return 'response|cache_static|' . $request->fingerprint();
    }
}
