<?php
/**
 * Model Type
 *
 * Assume you have a table that holds different types of audio files: podcasts, songs, snippets, etc. With
 * this trait you can set a column that will hold the type, while these models extend the same base model
 * with common properties and methods. This trait will add the scope and the type automatically to each.
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

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

trait ModelType
{
    /**
     * Boot the current trait.
     *
     * @return void
     */
    protected static function bootModelType()
    {
        static::addGlobalScope(function (Builder $builder) {
            $model = $builder->getModel();

            return $builder->where($model->getTypeQualifiedColumn(), $model->getTypeName());
        });
    }

    /**
     * Initialize the current trait.
     *
     * @return void
     */
    protected function initializeModelType()
    {
        $this->attributes[$this->getQualifiedTypeColumn()] = $this->getTypeName();
    }

    /**
     * Returns the name of the column that defines the model Type.
     *
     * @return string
     */
    protected function getQualifiedTypeColumn()
    {
        return 'type';
    }

    /**
     * Return the name of this class type.
     *
     * @return string
     */
    protected function getTypeName()
    {
        return Str::kebab(class_basename(static::class));
    }
}
