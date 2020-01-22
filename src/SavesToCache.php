<?php
/**
 * Saves to Cache
 *
 * This trait allows an object to be saved to the cache.
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

namespace DarkGhostHunter\Laratraits;

use LogicException;
use JsonSerializable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Htmlable;

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
     * The Cache Store to use
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function defaultCache() : Repository
    {
        return Cache::store();
    }

    /**
     * The key name to use in the cache if not specified.
     *
     * @return string
     */
    protected function defaultCacheKey()
    {
        throw new LogicException('The class ' . class_basename($this) . ' has no default cache key.');
    }

    /**
     * The value to insert into the cache.
     *
     * @return string|$this
     */
    protected function toCache()
    {
        if ($this instanceof Jsonable) {
            return $this->toJson();
        }

        if ($this instanceof JsonSerializable) {
            return json_encode($this);
        }

        if ($this instanceof Htmlable) {
            return $this->toHtml();
        }

        if (method_exists($this, '__toString')) {
            return $this->__toString();
        }

        return $this;
    }
}
