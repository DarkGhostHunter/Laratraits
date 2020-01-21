<?php

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
     * Saves the current object (or a part of it) to the cache.
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
