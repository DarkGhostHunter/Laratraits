<?php

namespace DarkGhostHunter\Laratraits;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Carbon;
use Serializable;

class CacheRegenerator implements Serializable
{
    /**
     * The time suffix to check the storing time.
     *
     * @var string
     */
    public static string $timeSuffix = ':time';

    /**
     * Cache repository to store and retrieve.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected Repository $store;

    /**
     * The object to cache.
     *
     * @var object
     */
    protected object $object;

    /**
     * The time the object became not equal to the source.
     *
     * @var \Illuminate\Support\Carbon|null
     */
    protected ?Carbon $invalidAt = null;

    /**
     * The cache key to use to operate.
     *
     * @var string
     */
    protected string $key;

    /**
     * CacheRegenerator constructor.
     *
     * @param  object  $object
     * @param  string|\Illuminate\Contracts\Cache\Repository  $store
     * @param  string  $key
     */
    public function __construct(object $object, $store, string $key)
    {
        $this->store = $store;
        $this->object = $object;
        $this->key = $key;
    }

    /**
     * Sets the object as no longer equal to the source.
     *
     * @param  bool  $forget
     */
    public function invalidate(bool $forget = false): void
    {
        $this->invalidAt = now();

        if ($forget) {
            $this->forget();
        }
    }

    /**
     * Forgets the cached data.
     *
     * @return void
     */
    public function forget(): void
    {
        $this->store->forget($this->key);
        $this->store->forget($this->key.':time');
    }

    /**
     * Regenerates the data in the cache.
     *
     * @param  int|\DateTimeInterface  $ttl
     * @param  bool  $force
     *
     * @return bool  Return "true" if stored, "false" if not.
     */
    public function regenerate($ttl = 60, bool $force = false): bool
    {
        if (!$force && !$this->shouldRegenerate()) {
            return false;
        }

        $this->store->setMultiple([
            $this->key.static::$timeSuffix => $now = now(),
            $this->key                     => method_exists($this->object,
                'toCache') ? $this->object->toCache() : $this->object,
        ], $ttl);

        $this->invalidAt = $now;

        return true;
    }

    /**
     * Check if the current data is fresher than the stored one.
     *
     * @return bool
     */
    protected function shouldRegenerate(): bool
    {
        if (!$this->invalidAt) {
            return true;
        }

        return $this->invalidAt->isAfter($this->store->get($this->key.static::$timeSuffix));
    }

    /**
     * String representation of object.
     *
     * @return string|null
     */
    public function serialize(): ?string
    {
        return null; // Don't serialize
    }

    /**
     * Constructs the object.
     *
     * @param  string  $data  The string representation of the object.
     *
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function unserialize($data): void
    {
        // Don't unserialize
    }
}
