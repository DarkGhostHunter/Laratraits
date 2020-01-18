<?php

namespace DarkGhostHunter\Laratraits;

/**
 * Interface RouteBound
 * ---
 * This interface obligates the developer to validate the incoming value from the frontend using an static
 * method, and then instantiate the class using another. If the validation doesn't passes, the validator
 * can either return `false`, or just throw a ValidationException, to protect the instancing itself.
 *
 * @package DarkGhostHunter\Laratraits
 */
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
