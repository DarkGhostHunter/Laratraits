<?php

namespace DarkGhostHunter\Laratraits;

/**
 * @deprecated Use Conditionable trait instead.
 * @see \Illuminate\Support\Traits\Conditionable
 */
class ConditionCallContainer
{
    /**
     * The target to call.
     *
     * @var object
     */
    protected object $target;

    /**
     * If the call should be passed through the target.
     *
     * @var bool
     */
    protected bool $pass = true;

    /**
     * Create a new Condition Call Container instance.
     *
     * @param  object  $target
     * @param  bool  $pass
     */
    public function __construct(object $target, bool $pass)
    {
        $this->target = $target;
        $this->pass = $pass;
    }

    /**
     * Manage a call into the object.
     *
     * @param  string  $name
     * @param  array  $arguments
     *
     * @return object
     */
    public function __call(string $name, array $arguments)
    {
        if ($this->pass) {
            $this->target->{$name}(...$arguments);
        }

        return $this->target;
    }
}
