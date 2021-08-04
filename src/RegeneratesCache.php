<?php
/**
 * RegeneratesCache
 *
 * This trait allows an object to be saved to the cache but avoiding data-races.
 *
 * The usage is simple: use `cache()->invalidate()` from the object to mark the
 * moment the object is no longer equal to the cache. Once ready to store, use
 * `cache()->regenerate()` to put a copy into the cache only if it's fresher.
 *
 *     $object->foo = bar;
 *     $object->cache()->invalidate();
 *
 *     // ...
 *
 *     $object->cache()->regenerate(); // Store a copy in the cache.
 *
 * If the object was not marked as invalid, or the data is not fresh, it won't
 * be stored into the cache, unless you force the regeneration.
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

use Illuminate\Contracts\Cache\Repository;

trait RegeneratesCache
{
    /**
     * Cache Regenerator instance.
     *
     * @var \DarkGhostHunter\Laratraits\CacheRegenerator|null
     */
    protected ?CacheRegenerator $regenerator = null;

    /**
     * Returns a cache regenerator.
     *
     * @return \DarkGhostHunter\Laratraits\CacheRegenerator
     */
    public function cache(): CacheRegenerator
    {
        return $this->regenerator ??= app(CacheRegenerator::class, [
            'object' => $this,
            'store' => $this->defaultCache(),
            'key' => $this->defaultCacheKey(),
        ]);
    }

    /**
     * The Cache Store to use to store this object.
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    abstract protected function defaultCache() : Repository;

    /**
     * The cache key name to use by default.
     *
     * @return string
     */
    abstract protected function defaultCacheKey() : string;
}
