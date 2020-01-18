<?php

namespace DarkGhostHunter\Laratraits;

interface RouteBound
{
    /**
     * Validates the Route Binding with the allowed values.
     *
     * @param string $value
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function validateRouteBinding(string $value) : bool;

    /**
     * Creates a new class instance using the value
     *
     * @param  string  $value
     * @return static|\DarkGhostHunter\Laratraits\RouteBound
     */
    public static function fromRouteBinding(string $value) : RouteBound;
}
