<?php

namespace DarkGhostHunter\Laratraits;

trait Throws
{
    /**
     * Throws the exception if a condition is truthy.
     *
     * @param  mixed  $condition
     * @param  mixed  ...$args
     * @return void
     *
     * @throws static
     */
    public static function when($condition, ...$args): void
    {
        if (value($condition)) {
            throw new static(...$args);
        }
    }

    /**
     * Throws the exception if a condition is falsy.
     *
     * @param mixed $condition
     * @param mixed ...$args
     * @return void
     *
     * @throws static
     */
    public static function unless($condition, ...$args): void
    {
        if (! value($condition)) {
            throw new static(...$args);
        }
    }
}
