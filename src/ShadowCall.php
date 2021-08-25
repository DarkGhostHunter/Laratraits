<?php

namespace DarkGhostHunter\Laratraits;

trait ShadowCall
{
    /**
     * Enables the next method call when the condition is truthy.
     *
     * @param  mixed  $condition
     *
     * @return static
     */
    public function callWhen($condition): ShadowProxy
    {
        return new ShadowProxy($this, $condition);
    }

    /**
     * Enables the next method call when the condition is falsy.
     *
     * @param  mixed  $condition
     *
     * @return static
     */
    public function callUnless($condition): ShadowProxy
    {
        return new ShadowProxy($this, $condition, false);
    }
}
