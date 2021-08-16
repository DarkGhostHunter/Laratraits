<?php
/**
 * UsesUuid
 *
 * This trait auto-fills an autogenerated UUID when the model is instanced. It also adds convenient
 * local scopes to the Eloquent Query Builder to find and filter this model through its UUID. For
 * additional performance, you can create an index on the UUID column in your model migrations.
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

use DarkGhostHunter\Laratraits\Scopes\UuidScope;
use Illuminate\Support\Str;

/**
 * @method \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null findUuid($uuid, $columns = ['*'])
 * @method \Illuminate\Database\Eloquent\Collection findManyUuid($uuid, $columns = ['*'])
 * @method \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|static[]|static|null findUuidOrFail($uuid, $columns = ['*'])
 * @method \Illuminate\Database\Eloquent\Model|static findUuidOrNew($uuid, $columns = ['*'])
 * @method \Illuminate\Database\Eloquent\Builder|static whereUuid($uuid)
 * @method \Illuminate\Database\Eloquent\Builder|static whereUuidNot($uuid)
 */
trait UsesUuid
{
    /**
     * Boot the UsesUuid trait.
     *
     * @return void
     */
    protected static function bootUsesUuid(): void
    {
        if (static::addUuidGlobalScope()) {
            static::addGlobalScope(new UuidScope);
        }
    }

    /**
     * Returns if this trait should add the local scope to the Eloquent Query Builder.
     *
     * @return bool
     */
    protected static function addUuidGlobalScope(): bool
    {
        return true;
    }

    /**
     * Initialize the UsesUuid trait.
     *
     * @return void
     */
    protected function initializeUsesUuid(): void
    {
        if (! $this->{$this->getUuidColumn()}) {
            $this->setAttribute($this->getUuidColumn(), $this->generateUuid());
        }
    }

    /**
     * Returns the column name where the UUID should be put.
     *
     * @return string
     */
    public function getUuidColumn(): string
    {
        return 'uuid';
    }

    /**
     * Returns an UUID to fill inside the model.
     *
     * @return \Ramsey\Uuid\UuidInterface|string
     */
    protected function generateUuid()
    {
        return Str::uuid();
    }
}
