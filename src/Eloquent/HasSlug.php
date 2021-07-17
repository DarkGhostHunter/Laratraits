<?php
/**
 * HasSlug
 *
 * This trait is just a handy collection of methods to allow any models to be routed by the "slug" on requests.
 * You'll need to set what text from the model use as a base to convert to an slug and, in your table, add the
 * "slug" string column with an unique for faster retrieval. The slug is generated automatically when saving.
 *
 * If you want to copy-paste the migration needed to your sluggable table model, use this:
 *
 *     $table->string('slug')->unique();
 *
 * To disable routing the model by the slug property, you can disable it using `routeBySlug`:
 *
 *    protected $routeBySlug = false;
 *
 * The above will use the parent Eloquent Model default routing key.
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

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the current trait.
     *
     * @return void
     */
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            // Set the slug if the model has not set it previously or the base been changed.
            if (! $model->getAttributeValue($model->getSlugKey()) || $model->isDirty($model->sluggableAttribute())) {
                $model->setSlug();
            }
        });
    }

    /**
     * Sets the URL slug from a given string.
     *
     * @return void
     */
    public function setSlug(): void
    {
        $value = $this->getAttribute($this->sluggableAttribute());

        $this->setAttribute($this->getSlugKey(), $this->slugValue($value));
    }

    /**
     * Transforms a given string to a slugged string.
     *
     * @param  string  $value
     * @return string
     */
    protected function slugValue(string $value): string
    {
        return Str::slug($value);
    }

    /**
     * Returns the attribute key that holds the string to slug.
     *
     * @return string
     */
    public function sluggableAttribute(): string
    {
        return 'title';
    }

    /**
     * Return the attribute that contains the slug.
     *
     * @return string
     */
    protected function getSlugKey(): string
    {
        return 'slug';
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return $this->routeBySlug ?? $this->getSlugKey();
    }
}
