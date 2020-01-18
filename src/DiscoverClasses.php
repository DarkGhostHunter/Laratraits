<?php

namespace DarkGhostHunter\Laratraits;

/**
 * Trait DiscoverClasses
 * ---
 * This class allows to spy inside a directory and check all PHP files containing classes a-la PSR-4.
 * You will receive a Collection made of all the instantiable classes, optionally filtered by a
 * method name (if the method is not present, the class is discarded) to tidy up the list.
 *
 * @package DarkGhostHunter\Laratraits
 */
trait DiscoverClasses
{
    /**
     * Discover instantiable classes from a given path.
     *
     * @param  string  $path  The Path to discover
     * @param  string  $method  The method name to filter the classes
     * @return \Illuminate\Support\Collection  A collection of class names.
     */
    protected function discover(string $path, string $method = null)
    {
        return ClassDiscoverer::from($path, base_path(), $method);
    }
}
