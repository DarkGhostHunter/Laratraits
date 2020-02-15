<?php
/**
 * Neighbour Records trait
 *
 * This trait will allow you to conveniently get the previous and next record in a performing-way. This only
 * returns the primary key of these neighbour records, but you can override the way the list is queried to
 * the database, and even if it should be cached or not, which is totally recommended for large tables.
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

trait NeighbourRecords
{
    /**
     * The chained records.
     *
     * @var array
     */
    protected $chained;

    /**
     * Gets the chained records to this model.
     *
     * @return array
     * @throws \Exception
     */
    protected function getChainedRecords()
    {
        if ($this->chained) {
            return $this->chained;
        }

        $list =  $this->getRecordsList();

        $index = $list->search(function ($item) {
            return $item->getKey() === $this->getKey();
        });

        return $this->chained = [
            'prev' => $list->get($index - 1),
            'next' => $list->get($index + 1),
        ];
    }

    /**
     * Returns the record list.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    protected function getRecordsList()
    {
        return cache()->remember("query|chapter_{$this->getKey()}|neighbours", 60, function () {
            return (new static)->latest()->get([$this->getKeyName()]);
        });
    }

    /**
     * Return the next record.
     *
     * @return null|$this
     * @throws \Exception
     */
    public function nextRecord()
    {
        return $this->getChainedRecords()['next'];
    }

    /**
     * Return the previous record.
     *
     * @return null|$this
     * @throws \Exception
     */
    public function prevRecord()
    {
        return $this->getChainedRecords()['prev'];
    }
}
