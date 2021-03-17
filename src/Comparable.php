<?php
/**
 * Comparable
 *
 * This trait allows an instance to be compared to a list of items and check if the comparison
 * returns true or false. By default, it checks the current object is an instance of the them.
 *
 * For example, you can check if the object is an instance of a given interface.
 *
 *     $this->isAnyOf([Renderable::class, Htmlable::class]);
 *
 * You can also create your own comparison with a callback that accepts the current object,
 * the item being compared to, and its key.
 *
 *     $this->isAnyOf([1, 2, 3], function ($comparable, $compared, $key) {
 *         return $comparable->timesSaved() === $comparable;
 *     });
 *
 * Alternatively, you can use the `isNoneOf` method to check if none of the comparisons is
 * successful, using the same syntax.
 *
 *     $this->isNoneOf([Renderable::class, Htmlable::class]);
 *
 *     $this->isNoneOf([1, 2, 3], function ($comparable, $compared, $key) {
 *         return $comparable->timesSaved() === $comparable;
 *     });
 *
 * Finally, you can get the array key of the compared item if is successful, otherwise it
 * will return `false`:
 *
 *     $result = $this->whichOf(['foo' => 'bar', 'baz' => 'quz'], function ($comparable, $compared) {
 *         return $comparable->name === $compared;
 *     });
 *
 *     echo $result; // "foo"
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
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits;

trait Comparable
{
    /**
     * Returns if any of the compared items comparison returns truthy.
     *
     * @param  iterable  $comparables
     * @param  null|callable  $callback
     * @param  bool  $returnKey
     * @return bool
     */
    public function isAnyOf(iterable $comparables, callable $callback = null, bool $returnKey = false)
    {
        $callback = $callback ?? static function ($compared, $comparable) {
            return $compared instanceof $comparable;
        };

        foreach ($comparables as $key => $comparable) {
            if ($result = $callback($this, $comparable, $key)) {
                return $returnKey ? $key : $result;
            }
        }

        return false;
    }

    /**
     * Returns if none of the compared items comparison returns truthy.
     *
     * @param  mixed  $comparables
     * @param  null|callable  $callback
     * @return bool
     */
    public function isNoneOf($comparables, callable $callback = null)
    {
        return $this->isAnyOf($comparables, $callback) === false;
    }

    /**
     * Returns which key of the compared items returns successful comparison.
     *
     * @param  mixed  $comparables
     * @param  null|callable  $callback
     * @return bool|int|string
     */
    public function whichOf($comparables, callable $callback = null)
    {
        return $this->isAnyOf($comparables, $callback, true);
    }
}
