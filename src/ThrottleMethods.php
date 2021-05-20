<?php
/**
 * ThrottleMethods
 *
 * This trait allows to rate limit a given action inside the object by a given key. It
 * returns the object instance instead of the result of the call.
 *
 *     $object->throttle(60, 1)->heavilyComputational($parameters);
 *
 * Alternatively, you can pass a callable to the limit that will be executed if there are
 * too many hits for the action. The callable receives the object as first as parameter
 * and the ActionRateLimiter as second parameter, where you can use the Rate Limiter.
 *
 *     $object->throttle(60, 1, function ($object, $limiter) {
 *         $limiter->throttlerClear('heavilyComputational');
 *         $object->doSomethingElse();
 *     })->heavilyComputational($parameters);
 *
 * If you need granular control on the cache key, use the "for()" method with the key:
 *
 *     $object->for($request->ip())->throttle(60, 1)->heavilyComputational($parameters);
 *
 * If this trait collides with another method in your class, you can import the trait and
 * change method name to another:
 *
 *     class Collides
 *     {
 *         use ThrottleMethods {
 *             for as throttleFor;
 *         };
 *
 *         // ...
 *     }
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
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2021 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits;

trait ThrottleMethods
{
    /**
     * Limits the next method call by tries inside a window of minutes.
     *
     * @param  int  $tries
     * @param  int  $minutes
     * @param  callable|null  $default
     * @return \DarkGhostHunter\Laratraits\ClassMethodThrottler
     */
    public function throttle($tries = 60, $minutes = 1, $default = null)
    {
        return $this->getActionThrottler()->throttle($tries, $minutes, $default);
    }

    /**
     * Limits the next method call for a given identifier.
     *
     * @param  string  $for
     * @return \DarkGhostHunter\Laratraits\ClassMethodThrottler
     */
    public function for(string $for)
    {
        return $this->getActionThrottler()->setKey($for);
    }

    /**
     * Clears the rate limiter for the method being throttled.
     *
     * @param  string  $method
     * @return \DarkGhostHunter\Laratraits\ThrottleMethods
     */
    public function throttleClear(string $method)
    {
        $this->getActionThrottler()->throttlerClear($method);

        return $this;
    }

    /**
     * Return the Action Throttler instance.
     *
     * @return \DarkGhostHunter\Laratraits\ClassMethodThrottler
     */
    protected function getActionThrottler()
    {
        return app(ClassMethodThrottler::class)->setTarget($this);
    }
}
