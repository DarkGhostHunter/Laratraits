<?php
/**
 * Default Columns
 *
 * This is a convenient way to make a Model only select certain columns by default. This may be handy to save
 * large chunks of memory when the retrieved record contains too much data that most of the time isn't used,
 * like walls of text, giant chunks of binary data, raw files encoded as base64 or a large list of columns.
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

use DarkGhostHunter\Laratraits\Scopes\DefaultColumns as DefaultColumnsScope;

trait DefaultColumns
{
    /**
     * Boot the SelectSomeColumns trait.
     *
     * @return void
     */
    protected static function bootDefaultColumns()
    {
        static::addGlobalScope(new DefaultColumnsScope(static::getDefaultColumns()));
    }

    /**
     * Get the Default Columns to query.
     *
     * @return array
     */
    protected static function getDefaultColumns()
    {
        return static::$defaultColumns ?? [];
    }
}
