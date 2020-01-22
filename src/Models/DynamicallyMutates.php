<?php

namespace DarkGhostHunter\Laratraits\Models;

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
     * @param  string  $value The attribute name to take.
     * @param  string  $type The attribute that holds the type
     * @return mixed
     */
    protected function castAttributeInto(string $value, string $type = null)
    {
        $type = $type ?? $value . '_type';

        // We will save the original casted attributes, swap them, and then restore them.
        $original = $this->casts;

        $this->casts = [
            $value => $this->attributes[$type],
        ];

        $attribute = $this->castAttribute($value, $this->attributes[$value]);

        $this->casts = $original;

        return $attribute;
    }
}
