<?php
/**
 * Has Slug URL
 *
 * This trait is just a handy collection of methods to allow any models to be routed by the "slug" on requests.
 * You need to set what string from the model use as a base to convert to an slug and, in your table, add the
 * "slug" string column with an index for faster retrieval. The slug is generated automatically when saving.
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
use Illuminate\Database\Eloquent\Model;

trait HasSlug
{
    /**
     * Boot the current trait.
     *
     * @return void
     */
    protected static function bootHasSlug()
    {
        static::saving(function (Model $model) {
            if (! $model->getAttribute('slug')) {
                $model->slug = $model->{$model->attributeToSlug()};
            }
        });
    }

    /**
     * Initialize the current trait.
     *
     * @return void
     */
    protected function initializeHasSlug()
    {
        if (! in_array('slug', $this->fillable, true)) {
            $this->fillable[] = 'slug';
        }
    }

    /**
     * Sets the URL slug from a given string.
     *
     * @param  string  $value
     * @return void
     */
    public function setSlugAttribute(string $value)
    {
        $this->attributes[$this->getSlugKey()] = Str::slug($value);
    }

    /**
     * Returns the attribute key that holds the string to slug.
     *
     * @return string
     */
    public function attributeToSlug()
    {
        return 'name';
    }

    /**
     * Return the attribute that contains the slug.
     *
     * @return string
     */
    protected function getSlugKey()
    {
        return 'slug';
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getSlugKey();
    }

}
