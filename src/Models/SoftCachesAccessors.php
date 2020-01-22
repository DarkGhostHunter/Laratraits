<?php
/**
 * SoftCaches Mutator
 *
 * This trait overrides the "mutateAttribute" from the Eloquent Model class and adds a logic to "cache" the
 * accessors used into the model instance. You may want to use this to avoid calling the logic every time
 * the accessor is used, specially if its logic is costly, like iterating a large set of data or else.
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

namespace DarkGhostHunter\Laratraits\Models;

use Closure;

trait SoftCachesAccessors
{
    /**
     * List of cached accessor values
     *
     * @var array
     */
    protected $cachedAccessorsValues = [];

    /**
     * Returns the value of an attribute using its cached accessor.
     *
     * @see \Illuminate\Database\Eloquent\Concerns\HasAttributes
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function mutateAttribute($key, $value)
    {
        if (! in_array($key, $this->cachedAccessors(), true)) {
            return parent::mutateAttribute($key, $value);
        }

        return $this->cachedAccessorsValues[$key] = $this->cachedAccessorsValues[$key]
            ?? parent::mutateAttribute($key, $value);
    }

    /**
     * Flush all the cached accessors
     *
     * @return $this
     */
    public function flushAccessorsCache()
    {
        $this->cachedAccessorsValues = [];

        return $this;
    }

    /**
     * Returns an attribute without using the cache.
     *
     * @param $key
     * @return mixed
     */
    public function getAttributeWithoutCache($key)
    {
        return parent::mutateAttribute($key, $this->getAttributeFromArray($key));
    }

    /**
     * Runs a callback without using the accessor cache
     *
     * @param  \Closure  $callback
     * @return mixed
     */
    public function withoutAccessorCache(Closure $callback)
    {
        $accessors = $this->cachedAccessorsValues;

        $this->flushAccessorsCache();

        $value = $callback($this);

        $this->cachedAccessorsValues = $accessors;

        return $value;
    }

    /**
     * The accessors to cache in the model
     *
     * @return array
     */
    protected function cachedAccessors()
    {
        return $this->cachedAccessors ?? [];
    }
}
