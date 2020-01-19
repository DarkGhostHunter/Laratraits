<?php

namespace DarkGhostHunter\Laratraits\Middleware;

use Closure;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Middleware ShareVerifiedUser
 * ---
 * This allows to share in all your views the "authenticated" variable containing the authenticated user, if any.
 *
 * @package DarkGhostHunter\Laratraits\Middleware
 */
class ShareVerifiedUser
{
    /**
     * The View Factory.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $factory;

    /**
     * The Authenticated user, if any.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected $user;

    /**
     * Create a new Share Authenticated User instance.
     *
     * @param  \Illuminate\Contracts\View\Factory  $factory
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     */
    public function __construct(Factory $factory, Authenticatable $user = null)
    {
        $this->factory = $factory;
        $this->user = $user;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $name
     * @return mixed
     */
    public function handle($request, Closure $next, string $name = 'authenticated')
    {
        $this->factory->share($name, $this->user);

        return $next($request);
    }
}
