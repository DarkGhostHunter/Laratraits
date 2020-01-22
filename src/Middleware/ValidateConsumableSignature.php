<?php

namespace DarkGhostHunter\Laratraits\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

/**
 * Class ValidateConsumableSignature
 * ---
 * Makes the signed request valid for only one time, except on request errors (4xx, 5xx).
 *
 * @package DarkGhostHunter\Laratraits\Middleware
 */
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
            return $next($request);
        }

        throw new InvalidSignatureException;
    }

    /**
     * Handle the sent response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return void
     */
    public function terminate($request, $response)
    {
        if (! $response->isServerError() && ! $response->isClientError()) {
            $this->consumeSignature($request);
        }
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
     * Return the cache Key to check
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    protected function cacheKey(Request $request)
    {
        return 'request|signature|' . $request->query('signature');
    }
}
