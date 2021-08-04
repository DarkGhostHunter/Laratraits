<?php
/**
 * ConditionCalls
 *
 * This trait allows a object to execute a given callback when a value or Closure evaluates
 * to true or false, without needing to use complex if/else blocks, in a fluent manner.
 *
 *     $object->when(true, function ($instance, $value) {
 *         $instance->lookAside($value);
 *     })->keepGoing();
 *
 * The same applies to the "unless" method, which will execute the callable only if the value
 * evaluates to false.
 *
 * Additionally, you can set a default callable to be executed, when the condition is false.
 *
 *     $object->unless(false, function ($instance, $value) {
 *         $instance->lookAside($value);
 *     }), function ($instance, $value) {
 *         $instance->coverEyes();
 *     })->keepDoingSomething();
 *
 * As always, if your object already has "when" and "unless" calls passed onto other objects
 * via dynamic calls ("__call()"), you can incorporate the trait with method aliasing:
 *
 *     class Object
 *     {
 *         use ConditionCall {
 *             when as whenThis,
 *             unless as unlessThis
 *         };
 *
 *         // ...
 *
 *     }
 *
 * Alternatively, if you don't supply a callback, it will catch the next method and call it
 * only when the value is true, or unless is false.
 *
 *     $object->when(true)->callThis()->thisWillBeCalledAnyway();
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

namespace DarkGhostHunter\Laratraits;

/**
 * @deprecated Use Conditionable trait instead.
 * @see \Illuminate\Support\Traits\Conditionable
 */
trait ConditionCalls
{
    /**
     * Execute the given callable when a value is truthy.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @param  callable|null $default
     * @return $this
     */
    public function when($value, callable $callback = null, callable $default = null)
    {
        $result = value($value);

        if (! $callback) {
            return new ConditionCallContainer($this, (bool) $result);
        }

        if ($result) {
            $callback($this, $result);
        } elseif ($default) {
            $default($this, $result);
        }

        return $this;
    }

    /**
     * Execute the given callable when a value is falsy.
     *
     * @param  mixed  $value
     * @param  callable|null  $callback
     * @param  callable|null $default
     * @return $this
     */
    public function unless($value, callable $callback = null, callable $default = null)
    {
        $result = value($value);

        if (! $callback) {
            return new ConditionCallContainer($this, ! $result);
        }

        if (! $result) {
            $callback($this, $result);
        } elseif ($default) {
            $default($this, $result);
        }

        return $this;
    }
}
