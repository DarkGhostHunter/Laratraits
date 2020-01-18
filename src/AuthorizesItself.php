<?php

namespace DarkGhostHunter\Laratraits;

use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Trait AuthorizesItself
 * ---
 * This takes a current object and allows to easily authorize certain actions using the application Gate.
 * To keep familiarity, these methods have the same name than the Authorizable and AuthorizesRequests
 * traits. If one of the methods are conflicting with these, you can use `insteadof` when using it.
 *
 * @package DarkGhostHunter\Laratraits
 */
trait AuthorizesItself
{
    /**
     * Determine if this entity has a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     *
     * @see \Illuminate\Foundation\Auth\Access\Authorizable
     */
    public function can($ability, $arguments = [])
    {
        return app(Gate::class)->check($ability, $arguments);
    }

    /**
     * Determine if this entity does not have a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     *
     * @see \Illuminate\Foundation\Auth\Access\Authorizable
     */
    public function cant($ability, $arguments = [])
    {
        return ! $this->can($ability, $arguments);
    }

    /**
     * Determine if this entity does not have a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     *
     * @see \Illuminate\Foundation\Auth\Access\Authorizable
     */
    public function cannot($ability, $arguments = [])
    {
        return $this->cant($ability, $arguments);
    }

    /**
     * Determine if all of the given abilities should be granted for this entity.
     *
     * @param  string  $ability
     * @param  array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @see \Illuminate\Foundation\Auth\Access\AuthorizesRequests
     */
    public function authorize($ability, $arguments = [])
    {
        return app(Gate::class)->authorize($ability, $arguments);
    }

    /**
     * Authorize a given action for a user for this entity.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|mixed  $user
     * @param  mixed  $ability
     * @param  mixed|array  $arguments
     * @return \Illuminate\Auth\Access\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @see \Illuminate\Foundation\Auth\Access\AuthorizesRequests
     */
    public function authorizeForUser($user, $ability, $arguments = [])
    {
        return app(Gate::class)->forUser($user)->authorize($ability, $arguments);
    }
}
