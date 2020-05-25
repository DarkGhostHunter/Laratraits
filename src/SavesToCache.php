<?php
/**
 * Saves to Cache
 *
 * This trait allows an object to be saved to the cache.
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

use LogicException;
use Illuminate\Contracts\Cache\Repository;

trait SavesToCache
{
    /**
     * Saves the current object to the cache.
     *
     * @param  string|null  $key
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     * @return bool
     */
    public function saveToCache(string $key = null, $ttl = 60)
    {
        return $this->defaultCache()->put($key ?? $this->defaultCacheKey(), $this->toCache(), $ttl);
    }

    /**
     * The Cache Store to use to store this object.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function defaultCache() : Repository
    {
        return cache()->store();
    }

    /**
     * The cache key name to use by default.
     *
     * @return string
     */
    protected function defaultCacheKey() : string
    {
        throw new LogicException('The class ' . static::class . ' has no default cache key.');
    }

    /**
     * The data to insert into the cache.
     *
     * @return $this
     */
    protected function toCache()
    {
        return $this;
    }
}
