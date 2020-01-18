<?php

namespace DarkGhostHunter\Laratraits\Controllers;

use LogicException;
use Illuminate\Http\Request;

/**
 * Trait CacheKeysRequest
 * ---
 * This traits allows to create a digestible cache key string based on the unique properties of the
 * Request itself, like the class being used to handle it, the Request IP it originates, the path
 * of the Request into the application, and, additionally, use the authenticated user auth id.
 *
 * @package DarkGhostHunter\Laratraits\Controllers
 */
trait CacheKeysRequest
{
    /**
     * Returns a digestible Cache Key based on the Request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function requestCacheKey(Request $request)
    {
        return $this->hashRequestCacheKey(get_class($this) . $request->ip() . $request->path());
    }

    /**
     * Returns a digestible Cache Key based on the Request and Authenticated User
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $guard
     * @return string
     */
    protected function requestCacheKeyWithAuth(Request $request, string $guard = null)
    {
        if (!$user = $request->user($guard)) {
            throw new LogicException('There is no authenticated user to create a request cache key');
        }
        return $this->hashRequestCacheKey(
            $this->requestCacheKey($request) . $request->user()->getAuthIdentifier()
        );
    }

    /**
     * Hashes the Request Data string
     *
     * @param  string  $data
     * @return string
     */
    protected function hashRequestCacheKey(string $data)
    {
        // We hash the Request into an MD5, but if you feel it you can use `hash('crc32b', $data)`.
        return md5($data);
    }
}
