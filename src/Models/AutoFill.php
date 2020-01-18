<?php

namespace DarkGhostHunter\Laratraits\Models;

use Illuminate\Support\Str;

/**
 * Trait AutoFill
 * ---
 * This trait will automatically fill a list of attributes by executing a method for each of them. These
 * methods must follow the "fillValueAttribute". For example, to fill the `foo` attribute, the method
 * `fillFooAttribute` must exists and return the value needed. Otherwise, try to use $attributes.
 *
 * @package DarkGhostHunter\Laratraits\Models
 */
trait AutoFill
{
    /**
     * Initialize the AutoFill trait
     *
     * @return void
     */
    protected function initializeAutoFill()
    {
        foreach ($this->autoFillable() as $value) {
            $this->{$key} = $this->{'fill' . Str::studly($value) . 'Attribute'}($value);
        }
    }

    /**
     * Returns an array of attributes to fill when the Model is instanced
     *
     * @return array
     */
    protected function autoFillable()
    {
        return [];
    }
}
