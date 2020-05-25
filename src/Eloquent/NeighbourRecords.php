<?php
/**
 * NeighbourRecords
 *
 * This trait will allow you to conveniently get the previous and next record in a performing-way. This only
 * returns the primary key of these neighbour records, but you can override the way the list is queried to
 * the database, and even if it should be cached or not, which is totally recommended for large tables.
 *
 * You can use it safely like this:
 *
 *     $post = Post::find(4);
 *
 *     $next = $post->nextRecord();
 *     $prev = $post->prevRecord();
 *
 *     if ($next) {
 *         echo 'The next podcast is ' . $next->id;
 *     }
 *
 * By default, it only selects the column used for routing, which is mostly the primary key. You can
 * override this in the `queryColumns()` method.
 *
 *     protected function queryColumns()
 *     {
 *         return ['id', 'title', 'user'];
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
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits\Eloquent;

use Illuminate\Database\Eloquent\Builder;

trait NeighbourRecords
{
    /**
     * The chained records.
     *
     * @var array
     */
    protected $neighbors;

    /**
     * Gets the chained records to this model.
     *
     * @return array
     */
    protected function getNeighbourRecords()
    {
        return $this->neighbors ?? $this->neighbors = $this->getRecordsList();
    }

    /**
     * Returns the record list.
     *
     * @return array
     */
    protected function getRecordsList()
    {
        return cache()
            ->remember("query|{$this->getQualifiedKeyName()}_{$this->getKey()}|neighbours", 60, function () {
                return [
                    'prev' => $this->queryPrevRecord(),
                    'next' => $this->queryNextRecord(),
                ];
            });
    }

    /**
     * Retrieves the previous model.
     *
     * @return null|static
     */
    protected function queryPrevRecord()
    {
        $builder = $this->latest()
            ->whereKeyNot($this->getKey())
            ->where($this->getCreatedAtColumn(), '<=', $this->{$this->getCreatedAtColumn()});

        $this->filterNeighbourQuery($builder);

        return $builder->first($this->queryColumns());
    }

    /**
     * Retrieves the next model.
     *
     * @return null|static
     */
    protected function queryNextRecord()
    {
        $builder = $this->oldest()
            ->whereKeyNot($this->getKey())
            ->where($this->getCreatedAtColumn(), '>=', $this->{$this->getCreatedAtColumn()});

        $this->filterNeighbourQuery($builder);

        return $builder->first($this->queryColumns());
    }

    /**
     * Filter the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void|\Illuminate\Database\Eloquent\Builder
     */
    protected function filterNeighbourQuery(Builder $builder)
    {
        //
    }

    /**
     * The columns to query the neighbour records.
     *
     * @return array|string[]
     */
    protected function queryColumns()
    {
        return [$this->getRouteKeyName()];
    }

    /**
     * Return the next record.
     *
     * @return null|$this
     * @throws \Exception
     */
    public function nextRecord()
    {
        return $this->getNeighbourRecords()['next'];
    }

    /**
     * Return the previous record.
     *
     * @return null|$this
     * @throws \Exception
     */
    public function prevRecord()
    {
        return $this->getNeighbourRecords()['prev'];
    }
}
