<?php

namespace DarkGhostHunter\Laratraits;

use LogicException;
use Illuminate\Support\Facades\Cache;

trait SavesToCache
{
    /**
     * Saves the current object (or a part of it) to the cache.
     *
     * @param  string|null  $key
     * @param  \DateTimeInterface|\DateInterval|int|null  $ttl
     */
    public function toCache(string $key = null, $ttl = 60)
    {
        $this->cacheStore()->put($key ?? $this->cacheKey(), $this->cacheValue(), $ttl);
    }

    /**
     * The Cache Store to use
     *
     * @return \Illuminate\Contracts\Cache\Repository
     */
    protected function cacheStore()
    {
        return Cache::store();
    }

    /**
     * The value to insert into the cache.
     *
     * @return $this
     */
    protected function cacheValue()
    {
        return $this;
    }

    /**
     * The key name to use in the cache.
     *
     * @return string
     */
    protected function cacheKey()
    {
        throw new LogicException('The class ' . class_basename($this) . ' has no default cache key.');
    }
}
