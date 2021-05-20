<?php
/**
 * ConditionFill
 *
 * This trait allows a model to be filled depending on a condition, allowing to chain methods.
 *
 * By default, if a condition is truthy, the attribute will be filled with the condition value.
 *
 *     $model->fillWhen($user->is_admin, 'read_only')->save();
 *     $model->fillUnless($request->isBot(), 'moderated')->save();
 *
 * You can also include a value to be set as the attribute:
 *
 *     $model->fillWhen($user->is_vip, 'cleared_at', now());
 *
 * The condition parameter also accepts a callable to resolve.
 *
 *     $model->fillWhen(fn() => return true, 'foo', 'bar');
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

trait ConditionFill
{
    /**
     * Fills an attribute when the condition is truthy.
     *
     * @param  mixed  $condition
     * @param  string  $attribute
     * @param  mixed  $value
     * @return $this
     */
    public function fillWhen($condition, string $attribute, $value = null)
    {
        if ($result = value($condition)) {
            $this->setAttribute($attribute, $value ?? $result);
        }

        return $this;
    }

    /**
     * Fills an attribute when the condition is falsy.
     *
     * @param  mixed  $condition
     * @param  string  $attribute
     * @param  mixed  $value
     * @return $this
     */
    public function fillUnless($condition, string $attribute, $value = null)
    {
        if (! $result = value($condition)) {
            $this->setAttribute($attribute, $value ?? $result);
        }

        return $this;
    }
}
