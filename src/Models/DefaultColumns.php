<?php

namespace DarkGhostHunter\Laratraits\Models;

use DarkGhostHunter\Laratraits\Scopes\DefaultColumns as DefaultColumnsScope;

/**
 * Trait DefaultColumns
 * ---
 * This is a convenient way to make a Model only select certain columns by default. This may be handy to save
 * large chunks of memory when the retrieved record contains too much data that most of the time isn't used,
 * like walls of text, giant chunks of binary data, raw files encoded as base64 or a large list of columns.
 *
 * @package DarkGhostHunter\Laratraits\Models
 */
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
