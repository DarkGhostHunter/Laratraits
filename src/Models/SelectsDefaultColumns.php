<?php

namespace DarkGhostHunter\Laratraits\Models;

use DarkGhostHunter\Laratraits\Scopes\DefaultColumns;

/**
 * Trait SelectsDefaultColumns
 * ---
 * This is a convenient way to make a Model only select certain columns by default. This may be handy to save
 * large chunks of memory when the retrieved record contains too much data that most of the time isn't used,
 * like walls of text, giant chunks of binary data, raw files encoded as base64 or a large list of columns.
 *
 * @package DarkGhostHunter\Laratraits\Models
 */
trait SelectsDefaultColumns
{
    /**
     * Default Selectable columns
     *
     * @var array
     */
    protected static $defaultColumns = [];

    /**
     * Boot the SelectSomeColumns trait.
     *
     * @return void
     */
    protected static function bootSelectsSomeColumns()
    {
        static::addGlobalScope(new DefaultColumns(static::$defaultColumns));
    }
}
