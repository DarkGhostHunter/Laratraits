<?php
/**
 * Validate Consumable Signature Middleware
 *
 * Makes the signed request valid only for one time, except on client (4xx) or server errors (5xx).
 *
 * You can set
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
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class ValidateConsumableSignature
{
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
     * @return mixed
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function handle($request, Closure $next)
    {
        if ($this->signatureNotConsumed($request) && $request->hasValidSignature()) {

            $response = $next($request);

            if (! $response->isServerError() && ! $response->isClientError()) {
                $this->consumeSignature($request);
            }

            return $response;
        }

        throw new InvalidSignatureException;
    }

    /**
     * Checks if the signature was consumed.
     *
     * @param  \Illuminate\Http\Request $request
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function signatureNotConsumed(Request $request)
    {
        return ! $this->cache->has($this->cacheKey($request));
    }

    /**
     * Consumes the signature, marking it as unavailable.
     *
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    protected function consumeSignature(Request $request)
    {
        $this->cache->put($this->cacheKey($request), null,
            Carbon::createFromTimestamp($request->query('expires')));
    }

    /**
     * Return the key to check in the cache for the request.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    protected function cacheKey(Request $request)
    {
        return 'request|signature|' . $request->query('signature');
    }
}
