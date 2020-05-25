<?php
/**
 * Throttles Requests
 *
 * This trait allows you to extend the Eloquent Builder instance using local macros, which are macros but
 * only valid for the instance itself instance of globally. This cycles through all the scope methods,
 * filters only those that starts with "macro", executes them receiving a Closure, and adds them.
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
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits\Scopes;

use ReflectionClass;
use ReflectionMethod;
use Illuminate\Database\Eloquent\Builder;

trait MacrosEloquent
{
    /**
     * Extend the Eloquent Query Builder instance with macros.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     * @throws \ReflectionException
     */
    public function extend(Builder $builder)
    {
        // We will cycle through all the public static methods in the present Scope instance
        // and add macros to the Builder instance by filtering those starting with "macro".
        // To say an example, the "macroAddOne()" method will be registered as "addOne()".
        foreach ((new ReflectionClass($this))
            ->getMethods(ReflectionMethod::IS_PUBLIC + ReflectionMethod::IS_STATIC) as $method) {
            if (strpos($name = $method->getName(), 'macro') === 0) {
                $builder->macro(lcfirst(substr($name, 5)), [static::class, $name]);
            }
        }
    }
}
