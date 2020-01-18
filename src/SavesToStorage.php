<?php

namespace DarkGhostHunter\Laratraits;

use LogicException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Jsonable;

trait SavesToStorage
{
    /**
     * Saves the current object (or a part of it) to session.
     *
     * @param  string|null  $path
     */
    public function toStorage(string $path = null)
    {
        $this->storageDisk()->put($path ?? $this->storagePath(), $this->storageContents());
    }

    /**
     * The Storage disk to use
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function storageDisk()
    {
        return Storage::disk();
    }

    /**
     * The value to insert into the Session.
     *
     * @return string
     */
    protected function storageContents()
    {
        if ($this instanceof Jsonable) {
            return $this->toJson();
        }

        if (method_exists($this, '__toString')) {
            return $this->__toString();
        }

        return serialize($this);
    }

    /**
     * The key name to use in the session.
     *
     * @return string
     */
    protected function storagePath()
    {
        throw new LogicException('The class ' . class_basename($this) . ' has no default storage path.');
    }
}
