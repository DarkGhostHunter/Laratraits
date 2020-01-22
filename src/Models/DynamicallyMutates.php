<?php
/**
 * Dynamically Mutates
 *
 * This trait allows a column to be mutated dynamically depending on a given value. For example, column "foo"
 * may contain a value that can be a boolean, array or string, while column "bar" contains its type. With
 * this, you can create an mutator/accessor to dynamically cast the value into its native data type.
 *
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

namespace DarkGhostHunter\Laratraits\Models;

trait DynamicallyMutates
{
    /**
     * Dynamically mutates an attribute by the other attribute value as "type".
     *
     * @param  string  $value The attribute name to take.
     * @param  string  $type The attribute that holds the type
     * @return mixed
     */
    protected function castAttributeInto(string $value, string $type = null)
    {
        $type = $type ?? $value . '_type';

        // We will save the original casted attributes, swap them, and then restore them.
        $original = $this->casts;

        $this->casts = [
            $value => $this->attributes[$type],
        ];

        $attribute = $this->castAttribute($value, $this->attributes[$value]);

        $this->casts = $original;

        return $attribute;
    }
}
