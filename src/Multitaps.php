<?php
/**
 * Multitaps
 *
 * This is a hacky way to "tap" indefinitely the current class instance. To stop multitaping, you can use
 * the "$target" public property to access the underlying object, or append "AndUntap" to any method.
 * This also allows for an optional callable to be used when starting the multitaping in here.
 *
 *     $result = $class->multitap()->foo()->bar($qux)->quzAndUntap();
 *
 * You can also use a callable to call before starting the multitap.
 *
 *     $result = $class->multitap(function ($class) {
 *         $class->doSomethingBeforeMultitap();
 *     })->foo()->barAndUntap();
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

trait Multitaps
{
    /**
     * Taps this current instance infinitely.
     *
     * @param  callable|null  $callable
     * @return \DarkGhostHunter\Laratraits\InfiniteHigherOrderTapProxy
     */
    public function multitap(callable $callable = null)
    {
        $multitap = new InfiniteHigherOrderTapProxy($this);

        if ($callable) {
            $callable($multitap->target);
        }

        return $multitap;
    }
}
