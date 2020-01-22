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
        foreach ($this->autoFillable() ?? [] as $attribute) {
            try {
                $result = $this->{'fill' . Str::studly($attribute) . 'Attribute'}($attribute);
            } catch (\BadMethodCallException $exception) {
                $method = 'fill' . Str::studly($attribute) . 'Attribute';
                throw new \BadMethodCallException(
                    "The attribute [$attribute] doesn't have a filler method [$method]."
                );
            }

            if (! isset($this->attributes[$attribute]) && $result) {
                $this->attributes[$attribute] = $result;
            }
        }
    }

    /**
     * Returns an array of attributes to fill when the Model is instanced
     *
     * @return array
     */
    protected function autoFillable()
    {
        return $this->autoFillable ?? [];
    }
}
