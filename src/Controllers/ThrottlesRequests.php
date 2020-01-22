<?php

namespace DarkGhostHunter\Laratraits\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

/**
 * Trait ThrottlesRequests
 * ---
 * This trait allows a controller action to be throttled. Basically, in your action, you use the `checkThrottle()`
 * method with the response, and use the `incrementsAttempts()` along with the minutes to decay. Defaults are
 * automatically set, but you can override them, allowing greater control on what and when to throttle.
 *
 * @package DarkGhostHunter\Laratraits\Controllers
 */
trait ThrottlesRequests
{
    /**
     * The Rate Limiter instance
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Execute an action on the controller.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @param  int  $attempts
     * @return \Symfony\Component\HttpFoundation\Response|void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkThrottle(Request $request = null, int $attempts = null)
    {
        $request = $request ?? app('request');

        if ($this->hasTooManyAttempts($request, $attempts)) {
            if (method_exists($this, 'fireThrottledEvent')) {
                $this->fireThrottledEvent($request);
            }

            $this->sendThrottledResponse($request);
        }
    }

    /**
     * Determine if the user has attempted too many times.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $attempts
     * @return bool
     */
    protected function hasTooManyAttempts(Request $request, int $attempts = null)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), $attempts ?? $this->maxAttempts()
        );
    }

    /**
     * Increment the attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $decay
     * @return void
     */
    protected function incrementAttempts(Request $request, int $decay = null)
    {
        $this->limiter()->hit(
            $this->throttleKey($request), ($decay ?? $this->decayMinutes()) * 60
        );
    }

    /**
     * Redirect the user after determining he is throttled.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response|void
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
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return 'request|throttle|' . $request->fingerprint();
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
    protected function maxAttempts()
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
