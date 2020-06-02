<?php
/**
 * ClassMethodThrottler
 *
 * This class conveniently catches an object and routes the method call to it only
 * when the method is not throttled. If a default callable is issued, it will be
 * called when the throttler hits the limit with the target and this instance.
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

namespace DarkGhostHunter\Laratraits;

use Illuminate\Cache\RateLimiter;

class ClassMethodThrottler
{
    /**
     * Target to rate limit.
     *
     * @var object
     */
    protected $target;

    /**
     * Rate Limiter instance.
     *
     * @var \Illuminate\Cache\RateLimiter
     */
    protected $limiter;

    /**
     * Maximum tries inside the decay window.
     *
     * @var int
     */
    protected $maxAttempts;

    /**
     * Window of seconds to make tries.
     *
     * @var int
     */
    protected $decaySeconds;

    /**
     * Default callable to call when hitting the limit.
     *
     * @var callable|null
     */
    protected $default;

    /**
     * Custom Key to use.
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new ActionRateLimiter instance.
     *
     * @param  \Illuminate\Cache\RateLimiter  $limiter
     */
    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Sets the limits to use with the Rate Limiter.
     *
     * @param  int  $tries
     * @param  int  $minutes
     * @param  callable|null  $default
     * @return \DarkGhostHunter\Laratraits\ClassMethodThrottler
     */
    public function throttle(int $tries, int $minutes, callable $default = null)
    {
        $this->maxAttempts = $tries;
        $this->decaySeconds = $minutes * 60;
        $this->default = $default;

        return $this;
    }

    /**
     * Use a custom Key instead of the default one.
     *
     * @param  string  $key
     * @return \DarkGhostHunter\Laratraits\ClassMethodThrottler
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Sets the target object to throttle its methods.
     *
     * @param  object  $target
     * @return $this
     */
    public function setTarget(object $target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Clear the throttler for a given method.
     *
     * @param  string  $method
     * @return \DarkGhostHunter\Laratraits\ClassMethodThrottler
     */
    public function throttlerClear(string $method)
    {
        $this->limiter->clear($this->actionRateLimiterKey($method));

        return $this;
    }

    /**
     * Checks if there are too many attempts for the method.
     *
     * @param  string  $method
     * @return bool
     */
    public function throttlerTooManyAttempts(string $method)
    {
        return $this->limiter->tooManyAttempts($this->actionRateLimiterKey($method), $this->maxAttempts);
    }

    /**
     * Hits the throttler for the given method.
     *
     * @param  string  $method
     * @return mixed
     */
    public function throttlerHit(string $method)
    {
        $this->limiter->hit($this->actionRateLimiterKey($method), $this->decaySeconds);

        return $this;
    }

    /**
     * Returns the Action Rate Limiter key.
     *
     * @param string|null $name
     * @return string
     */
    protected function actionRateLimiterKey(string $name)
    {
        return implode('|' , array_filter([
            'class_method_throttler', $this->key, get_class($this->target) . '@' . $name
        ]));
    }

    /**
     * Handle dynamically calling the object.
     *
     * @param  string  $name
     * @param  mixed  $arguments
     * @return object
     */
    public function __call($name, $arguments)
    {
        if (! $this->throttlerTooManyAttempts($name)) {
            $this->target->{$name}(...$arguments);
            $this->throttlerHit($name);
        }
        else {
            with($this->target, $this->default);
        }

        return $this->target;
    }
}
