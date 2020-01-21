<?php

namespace DarkGhostHunter\Laratraits;

use Illuminate\Support\Str;
use Illuminate\Support\HigherOrderTapProxy;

class InfiniteHigherOrderTapProxy extends HigherOrderTapProxy
{
    /**
     * Dynamically pass method calls to the target.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // If the method to calls ends with "AndUntap", we will redirect the method original
        // name to the target class, and return the result. Otherwise, we will keep pushing
        // each of the methods incoming to the underlying class instance and return this.
        if (Str::endsWith($method, 'AndUntap')) {
            return $this->target->{substr($method, 0, -8)}(...$parameters);
        }

        parent::__call($method, $parameters);

        return $this;
    }
}
