<?php

namespace DarkGhostHunter\Laratraits\Models;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Trait DynamicallyMutates
 * ---
 * This trait allows a column to be mutated dynamically depending on a given value. For example, column "foo"
 * may contain a value that can be a boolean, array or string, while column "bar" contains its type. With
 * this, you can create an mutator/accessor to dynamically cast the value into its native data type.
 *
 * @package DarkGhostHunter\Laratraits\Models
 */
trait DynamicallyMutates
{
    /**
     * Dynamically mutates an attribute by the other attribute value as "type".
     *
     * @param  string  $type
     * @param $value
     * @return mixed
     */
    protected function castValueInto($value, string $type)
    {
        // This is a hacky way to reuse the same casts array for our own bidding.
        $original = $this->casts;

        $this->casts = [
            'value' => $this->{$type}
        ];

        $value = $this->castAttribute('value', $value);

        $this->casts = $original;

        return $value;
    }
}
