<?php

namespace DarkGhostHunter\Laratraits;

/**
 * Trait DiscoverClasses
 * ---
 * This class allows to spy inside a directory and check all PHP files containing classes a-la PSR-4.
 * You will receive a Collection made of all the instantiable classes, optionally filtered by a
 * method name or implementation interface. You will receive a collection of the classes.
 *
 * @package DarkGhostHunter\Laratraits
 */
trait DiscoverClasses
{
    /**
     * Discover instantiable classes from a given path.
     *
     * @param  string  $path  The Path to discover. Defaults to the
     * @param  string|null  $methodOrInterface The Method name or Interface to filter the classes.
     * @return \Illuminate\Support\Collection  A collection of filtered class names.
     */
    protected function discover(string $path, string $methodOrInterface = null)
    {
        $discoverer = (new ClassDiscoverer())->path($path);

        if ($methodOrInterface) {
            if (interface_exists($methodOrInterface)) {
                $discoverer->filterByInterface($methodOrInterface);
            } else {
                $discoverer->filterByMethod($methodOrInterface);
            }
        }

        return $discoverer->discover();
    }
}
