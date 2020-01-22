<?php

namespace DarkGhostHunter\Laratraits\Scopes;

use Illuminate\Database\Eloquent\Builder;

/**
 * Trait MacrosEloquent
 * ---
 * This trait allows you to extend the Eloquent Builder instance using local macros, which are macros but
 * only valid for the instance itself instance of globally. This cycles through all the scope methods,
 * filters only those that starts with "macro", executes them receiving a Closure, and adds them.
 *
 * @package DarkGhostHunter\Laratraits\Scopes
 */
trait MacrosEloquent
{
    /**
     * Extend the query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        // We will cycle through all the present methods in the present Scope instance and
        // add macros for only the methods who start with "macro" that return a Closure.
        // For example, the "macroAddOne()" method will be registered as "addOne()".
        foreach (get_class_methods($this) as $method) {
            if (strpos($method, 'macro') === 0) {
                $builder->macro(lcfirst(substr($method, 5)), $this->{$method}($builder));
            }
        }
    }
}
