<?php

namespace DarkGhostHunter\Laratraits\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

/**
 * Trait ThrottlesRequest
 * ---
 * This trait allows your Controller to throttle a given Request with more control. It's recommended to use
 * `checkThrottling` and `incrementAttempts` for easy setup in your Controller method, and only one of
 * them being throttled, but you can still go wild and use this trait for fine grain throttling.
 *
 * @package DarkGhostHunter\Laratraits\Controllers
 *
 * @see \Illuminate\Foundation\Auth\ThrottlesLogins
 */
trait ThrottlesRequest
{
    use CacheKeysRequest;

    /**
     * The Rate Limiter instance
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Check if the request should be throttled
     *
     * @param $request
     * @param  bool  $hit
     * @param  int|null  $maxAttempts
     * @param  int|null  $decayMinutes
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function checkThrottling($request,
                                       bool $hit = false,
                                       int $maxAttempts = null,
                                       int $decayMinutes = null)
    {
        if ($this->hasTooManyAttempts($request, $maxAttempts)) {

            if (method_exists($this, 'fireThrottledEvent')) {
                $this->fireThrottledEvent($request, $this->throttleKey($request));
            }

            $this->sendThrottledResponse($request);
        }

        if ($hit) {
            $this->incrementAttempts($request, $decayMinutes);
        }
    }

    /**
     * Determine if the user has attempted too many times.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int|null $maxAttempts
     * @return bool
     */
    protected function hasTooManyAttempts(Request $request, int $maxAttempts = null)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $maxAttempts ?? $this->maxAttempts()
        );
    }

    /**
     * Increment the attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $decayMinutes
     * @return void
     */
    protected function incrementAttempts(Request $request, int $decayMinutes = null)
    {
        $this->limiter()->hit(
            $this->throttleKey($request), ($decayMinutes ?? $this->decayMinutes()) * 60
        );
    }

    /**
     * Redirect the user after determining he is throttled.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendThrottledResponse(Request $request)
    {
        throw ValidationException::withMessages([
            Lang::get('auth.throttle', ['seconds' => $this->availableIn($request)])
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Returns how many seconds until the request can be retried again
     *
     * @param  \Illuminate\Http\Request $request
     * @return integer
     */
    protected function availableIn(Request $request)
    {
        return $this->limiter()->availableIn($this->throttleKey($request));
    }

    /**
     * Clear the number of attempts for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return $this->requestCacheKey($request);
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return $this->limiter = $this->limiter ?? app(RateLimiter::class);
    }

    /**
     * Get the maximum number of attempts to allow.
     *
     * @return int
     */
    public function maxAttempts()
    {
        return $this->maxAttempts ?? 5;
    }

    /**
     * Get the number of minutes to throttle for.
     *
     * @return int
     */
    public function decayMinutes()
    {
        return $this->decayMinutes ?? 1;
    }
}
