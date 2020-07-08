<?php

namespace DarkGhostHunter\Laratraits;

class ConditionCallContainer
{
    /**
     * The target to call.
     *
     * @var object
     */
    protected $target;

    /**
     * If the call should be passed through the target.
     *
     * @var bool
     */
    protected $pass = true;

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
     * @return object
     */
    public function __call($name, $arguments)
    {
        if ($this->pass) {
            $this->target->{$name}(...$arguments);
        }

        return $this->target;
    }
}