<?php
/**
 * FillsAttributes
 *
 * This trait will automatically fill a list of attributes by executing a method for each of them. These
 * methods must follow the "fillValueAttribute". For example, to fill the `foo` attribute, the method
 * `fillFooAttribute` must exists and return the value needed. Otherwise, try to use $attributes.
 *
 *     protected $autoFillable = ['foo'];
 *
 *     protected function fillFooAttribute($value)
 *     {
 *         $this->attributes['foo'] = $value;
 *     }
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

namespace DarkGhostHunter\Laratraits\Eloquent;

use BadMethodCallException;
use Illuminate\Support\Str;

trait FillsAttributes
{
    /**
     * Initialize the AutoFill trait
     *
     * @return void
     */
    protected function initializeFillsAttributes()
    {
        foreach ($this->autoFillable() as $attribute) {

            if (isset($this->attributes[$attribute])) {
                continue;
            }

            try {
                $result = $this->{'fill' . Str::studly($attribute) . 'Attribute'}();
            } catch (BadMethodCallException $exception) {
                throw new BadMethodCallException(
                    "The attribute [$attribute] has no a filler method [fill".Str::studly($attribute)."Attribute]."
                );
            }

            if ($result && ! isset($this->attributes[$attribute])) {
                $this->setAttribute($attribute, $result);
            }
        }
    }

    /**
     * Returns an array of attributes to fill when the Model is instanced
     *
     * @return array
     */
    protected function autoFillable()
    {
        return $this->autoFillable ?? [];
    }
}
