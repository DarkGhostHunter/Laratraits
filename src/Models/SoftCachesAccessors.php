<?php

namespace DarkGhostHunter\Laratraits\Models;

use Closure;

/**
 * Trait SoftCachesMutator
 * ---
 * This trait overrides the "mutateAttribute" from the Eloquent Model class and adds a logic to "cache" the
 * accessors used into the model instance. You may want to use this to avoid calling the logic every time
 * the accessor is used, specially if its logic is costly, like iterating a large set of data or else.
 *
 * @package DarkGhostHunter\Laratraits\Models
 */
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
