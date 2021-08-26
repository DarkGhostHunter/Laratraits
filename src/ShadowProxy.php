<?php

namespace DarkGhostHunter\Laratraits;

use Closure;
use Illuminate\Support\Traits\ForwardsCalls;

class ShadowProxy
{
    use ForwardsCalls;

    /**
     * The target object.
     *
     * @var object
     */
    public object $target;

    /**
     * The condition to evaluate
     *
     * @var mixed
     */
    protected $condition;

    /**
     * If it should execute the method when the condition is truthy.
     *
     * @var bool
     */
    protected bool $when;

    /**
     * Create a new shadow proxy
     *
     * @param  object  $object
     * @param  mixed  $condition
     * @param  bool  $when
     *
     * @return void
     */
    public function __construct(object $object, $condition = null, bool $when = true)
    {
        $this->target = $object;
        $this->condition = $condition;
        $this->when = $when;
    }

    /**
     * Handles the dynamic call to the underlying shadow proxy object.
     *
     * @param  string  $name
     * @param  array  $arguments
     *
     * @return object
     */
    public function __call(string $name, array $arguments): object
    {
        $condition = $this->condition instanceof Closure ? ($this->condition)(...$arguments) : $this->condition;

        if ($this->when === (bool) $condition) {
            $this->forwardCallTo($this->target, $name, $arguments);
        }

        return $this->target;
    }
}
