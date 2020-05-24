<?php
/**
 * CastRepository
 *
 * This Cast allows a column on the model with complex arrayable data to ve a Repository.
 * This allows the property to be accessed like a config-style set of values.
 *
 *     protected $casts = [
 *         'config' => CastRepository::class,
 *     ];
 *
 * Then, in your code, you can use the Repository methods.
 *
 *     $model->config->get('schedule.days');
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

namespace DarkGhostHunter\Laratraits\Eloquent\Casts;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CastsRepository implements CastsAttributes
{
    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return new Repository($value);
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if ($value instanceof Repository) {
            return $value->all();
        }

        return (array)$value;
    }
}
