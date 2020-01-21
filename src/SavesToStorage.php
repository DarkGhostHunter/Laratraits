<?php

namespace DarkGhostHunter\Laratraits;

use LogicException;
use JsonSerializable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Htmlable;

/**
 * Trait SavesToStorage
 * ---
 * This trait allows an object to be saved into a storage disk with convenient methods. Since it's very
 * improbable to handle multiple storage disks, there is no "default" disk but only the one you set.
 * Remember to always ensure you set a string or anything serializable, along with a valid path.
 *
 * @package DarkGhostHunter\Laratraits
 */
trait SavesToStorage
{
    /**
     * Persists the current object data to the storage.
     *
     * @param  string|null  $path
     */
    public function saveToStore(string $path = null)
    {
        $this->defaultStorage()->put($path ?? $this->defaultStoragePath(), $this->toStore());
    }

    /**
     * The Storage disk to use.
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function defaultStorage()
    {
        return Storage::disk();
    }

    /**
     * The key name to use in the session.
     *
     * @return string
     */
    protected function defaultStoragePath() : string
    {
        throw new LogicException('The class ' . class_basename($this) . ' has no default storage path.');
    }

    /**
     * Get content that should be persisted into the Storage.
     *
     * @return string|$this
     */
    protected function toStore()
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
