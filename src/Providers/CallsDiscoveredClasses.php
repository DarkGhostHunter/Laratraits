<?php

namespace DarkGhostHunter\Laratraits\Providers;

use Illuminate\Support\Collection;
use DarkGhostHunter\Laratraits\DiscoverClasses;

/**
 * Trait CallsDiscoveredClasses
 * ---
 * This trait is just a convenient way to discover classes following certain filter and do callback on each.
 * You can do this, for example, to register extra Service Providers, bind or singleton classes into the
 * Service Container, auto-discover files and extend Services or other classes, the sky is the limit!
 *
 * @package DarkGhostHunter\Laratraits\Providers
 */
trait CallsDiscoveredClasses
{
    use DiscoverClasses;

    /**
     * Discover classes inside a given path and runs a callable for each of them.
     *
     * @param  string  $path  The path to look for classes.
     * @param  callable  $callable  The callable to use for each class string.
     * @param  callable|null  $filter  The optional filter for the classes list.
     * @return void
     */
    protected function callClasses(string $path, callable $callable, callable $filter = null)
    {
        $this->discover($path)->when($filter, function (Collection $classes) use ($filter) : Collection {
            return $classes->filter($filter);
        })->each($callable);
    }
}
