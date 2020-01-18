<?php

namespace DarkGhostHunter\Laratraits\Providers;

use DarkGhostHunter\Laratraits\RouteBound;

/**
 * Trait RouteBindsClasses
 * ---
 * This class allows a given class to be instantiated using the Route value. To avoid mal practices (like
 * blindly construct objects from unvalidated values) all of these classes in the list must implement
 * the RouteBound interface to validate the value from the Request, and make a new class instance.
 *
 * @package DarkGhostHunter\Laratraits\Providers
 */
trait RouteBindsClasses
{
    /**
     * Classes to explicitly route bind
     *
     * @var array
     * @example ['transport' => \App\Transport\TransportManager::class]
     */
    protected $boundClasses = [];

    /**
     * Route bind an array of classes. Call this in your `boot()` method.
     *
     * @return void
     */
    protected function routeBindClasses()
    {
        if ($this->boundClasses === []) {
            return;
        }

        $router = $this->app->make('router');

        foreach ($this->boundClasses as $key => $class) {
            $router->bind($key, function($value) use ($class) {

                if (! class_implements($class, RouteBound::class)) {
                    throw new \LogicException("The $class does not implements the RouteBound interface.");
                }

                if ($class::validateRouteBinding($value)) {
                    return $class::fromRouteBinding($value);
                }

                return null;
            });
        }
    }
}
