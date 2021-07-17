<?php
/**
 * Uuid Scope
 *
 * This conveniently adds local scopes to handle UUIDs to the Eloquent Query Builder. These scopes are only
 * valid for the Builder instance itself, and doesn't interfere with other builders of other models. You
 * can register this Scope all by yourself, but it's better to use the UsesUuid trait in your models.
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

namespace DarkGhostHunter\Laratraits\Scopes;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Scope;

class UuidScope implements Scope
{
    use MacrosEloquent;

    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model): void
    {
        //
    }

    /**
     * Find a model by its UUID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  string|array|\Illuminate\Contracts\Support\Arrayable  $uuid
     * @param  string[]  $columns
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function macroFindUuid(Builder $builder, $uuid, array $columns = ['*'])
    {
        if (is_array($uuid) || $uuid instanceof Arrayable) {
            return $builder->findManyUuid($uuid, $columns);
        }

        return $builder->whereUuid($uuid)->first($columns);
    }

    /**
     * Find multiple models by their UUID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  string|array|\Illuminate\Contracts\Support\Arrayable  $uuids
     * @param  string[]  $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function macroFindManyUuid(Builder $builder, $uuids, array $columns = ['*']): Collection
    {
        $uuids = $uuids instanceof Arrayable ? $uuids->toArray() : $uuids;

        if (empty($uuids)) {
            return $builder->getModel()->newCollection();
        }

        return $builder->whereUuid($uuids)->get($columns);
    }

    /**
     * Find a model by its UUID or throw an exception.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  string|array|\Illuminate\Contracts\Support\Arrayable  $uuid
     * @param  string[]  $columns
     *
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model[]
     */
    public static function macroFindUuidOrFail(Builder $builder, $uuid, array $columns = ['*'])
    {
        $result = $builder->findUuid($uuid, $columns);

        if (is_array($uuid)) {
            if (count($result) === count(array_unique($uuid))) {
                return $result;
            }
        }
        elseif ($result !== null) {
            return $result;
        }

        throw (new ModelNotFoundException)->setModel(
            get_class($builder->getModel()), $uuid
        );
    }

    /**
     * Find a model by its UUID or return fresh model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  mixed  $uuid
     * @param  string[]  $columns
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function macroFindUuidOrNew(Builder $builder, $uuid, array $columns = ['*']): Model
    {
        if (($model = $builder->findUuid($uuid, $columns)) !== null) {
            return $model;
        }

        return $builder->newModelInstance();
    }

    /**
     * Add a where clause on the UUID column to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  string|array|\Illuminate\Contracts\Support\Arrayable  $uuid
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function macroWhereUuid(Builder $builder, $uuid): Builder
    {
        if (is_array($uuid) || $uuid instanceof Arrayable) {
            $builder->getQuery()->whereIn(
                $builder->getModel()->getUuidColumn(), $uuid
            );

            return $builder;
        }

        return $builder->where($builder->getModel()->getUuidColumn(), '=', $uuid);
    }

    /**
     * Add a where clause on the UUID column key to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  string|array|\Illuminate\Contracts\Support\Arrayable $uuid
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function macroWhereUuidNot(Builder $builder, $uuid): Builder
    {
        if (is_array($uuid) || $uuid instanceof Arrayable) {
            $builder->getQuery()->whereNotIn(
                $builder->getModel()->getUuidColumn(), $uuid
            );

            return $builder;
        }

        return $builder->where($builder->getModel()->getUuidColumn(), '!=', $uuid);
    }

}
