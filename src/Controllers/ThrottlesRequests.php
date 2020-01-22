<?php
/**
 * Throttles Requests
 *
 * This trait allows a controller action to be throttled. Basically, in your action, you use the `checkThrottle()`
 * method with the response, and use the `incrementsAttempts()` along with the minutes to decay. Defaults are
 * automatically set, but you can override them, allowing greater control on what and when to throttle.
 *
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

namespace DarkGhostHunter\Laratraits\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;

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
