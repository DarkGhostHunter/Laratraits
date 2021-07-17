<?php
/**
 * Model Type
 *
 * Assume you have a table that holds different types of audio files: podcasts, songs, snippets, etc. With
 * this trait you can set a column that will hold the type, while these models extend the same base model
 * with common properties and methods. This trait will add the scope and the type automatically to each.
 *
 *     class Color extends Model
 *     {
 *         protected $table = 'colors';
 *
 *         // ..
 *     }
 *
 *     class Red extends Color
 *     {
 *         use ModelType;
 *     }
 *
 *     class Blue extends Color
 *     {
 *         use ModelType;
 *     }
 *
 * When you save "Red" or "Blue" models, your table would look like this:
 *
 *     | colors                              |
 *     | id | type | created_at | updated_at |
 *     |----|------|------------|------------|
 *     | 1  | red  | 2020-04-01 | 2020-04-01 |
 *     | 2  | blue | 2020-04-01 | 2020-04-01 |
 *
 * If you constantly query the column type, is recommended to (at least) create an index for it.
 *
 *     $table->index('type');
 *
 * You can also go wild with compound primary keys, if necessary.
 *
 *     $table->dropPrimary('id');
 *     $table->primary(['id', 'type']);
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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use LogicException;

trait ModelType
{
    /**
     * Boot the current trait.
     *
     * @return void
     */
    protected static function bootModelType(): void
    {
        static::addGlobalScope(function (Builder $builder) {
            $model = $builder->getModel();

            return $builder->where($model->getModelTypeColumn(), $model->getModelType());
        });
    }

    /**
     * Initialize the current trait.
     *
     * @return void
     */
    protected function initializeModelType(): void
    {
        // Becase Eloquent will get the name of the table from the model name itself, we will have
        // to bail out if the table name has not been explicitly set by the developer. Otherwise,
        // when querying the child models, Eloquent will look for them by using missing tables.
        if ($this->table === null) {
            throw new LogicException(
                'The ' . static::class . ' model must set a common table name for all extending models.'
            );
        }

        $this->attributes[$this->getModelTypeColumn()] = $this->getModelType();
    }

    /**
     * Returns the name of the column that defines the model Type.
     *
     * @return string
     */
    public function getModelTypeColumn(): string
    {
        return 'type';
    }

    /**
     * Return the name of this class type.
     *
     * @return string
     */
    public function getModelType(): string
    {
        return Str::snake(class_basename(static::class));
    }
}
