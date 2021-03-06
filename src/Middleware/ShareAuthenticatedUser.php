<?php
/**
 * ShareAuthenticatedUser
 *
 * This allows to share in all your views the "authenticated" variable containing the
 * authenticated user, if any.
 *
 * It's recommended to add it in your HTTP Kernel as part of the 'web' stack of middleware:
 *
 *     protected $middlewareGroups = [
 *         'web' => [
 *             // ...
 *
 *             \DarkGhostHunter\Laratraits\Middleware\ShareAuthenticatedUser::class
 *         ];
 *     ];
 *
 * Then, in your views, you can use the "$authenticated" variable anywhere, which can be "null"
 * if not authenticated.
 *
 *     @auth
 *         <h2>Welcome, {{ $authenticated->name }}</h2>
 *     @endauth
 *
 * ---
 * MIT License
 *
 * Copyright (c) Italo Israel Baeza Cabrera
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2021 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;

class ShareAuthenticatedUser
{
    /**
     * The View Factory.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected Factory $factory;

    /**
     * The Authenticated user, if any.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable|null
     */
    protected ?Authenticatable $user;

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
    public function handle(Request $request, Closure $next, string $name = 'authenticated')
    {
        $this->factory->share($name, $this->user);

        return $next($request);
    }
}
