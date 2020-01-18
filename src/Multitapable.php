<?php

namespace DarkGhostHunter\Laratraits;

/**
 * Trait Multitapable
 * ---
 * This is a hacky way to "tap" indefinitely the current class instance. To stop multitaping, you can use
 * the "$target" public property to access the underlying object, or append "AndUntap" to any method.
 * This also allows for an optional callable to be used when starting the multitaping in here.
 *
 * @package DarkGhostHunter\Laratraits
 */
trait Multitapable
{
    /**
     * Taps this current instance infinitely.
     *
     * @param  callable|null  $callable
     * @return \DarkGhostHunter\Laratraits\InfiniteHigherOrderTapProxy
     * @see \DarkGhostHunter\Laratraits\InfiniteHigherOrderTapProxy
     */
    public function multitap(callable $callable = null)
    {
        $multitap = new InfiniteHigherOrderTapProxy($this);

        if ($callable) {
            $callable($multitap->target);
        }

        return $multitap;
    }
}
