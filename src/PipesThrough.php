<?php

namespace DarkGhostHunter\Laratraits;

use Closure;

/**
 * Trait PipesThrough
 * ---
 * This trait is fairly simple: takes the instance and sends it through a given Pipeline, instantiated by
 * the Service Container, and returns a result (hopefully the same instance). You can change the pipes,
 * the Closure to receive the result, the default methods to use, and even the Pipeline class itself.
 *
 * @package DarkGhostHunter\Laratraits
 */
trait PipesThrough
{
    /**
     * Pipeline to instantiate.
     *
     * @see \Illuminate\Contracts\Pipeline\Pipeline
     * @var string The class to instantiate.
     */
    protected $pipeline = \Illuminate\Pipeline\Pipeline::class;

    /**
     * Pipes the current instance into a pipeline.
     *
     * @param  array  $pipes  The list of pipes (in order) to send this class instance
     * @param  \Closure|null  $destination  Optional closure that will receive the result
     * @param  string|null  $via  Optional alternative common handling method for all the pipelines
     * @return $this|mixed
     */
    public function pipe(array $pipes = [], Closure $destination = null, string $via = null)
    {
        $pipeline = app($this->pipeline)->send($this);

        if (! $pipes === []) {
            $pipeline->through($pipes);
        }

        if ($via) {
            $pipeline->via($via);
        }

        return $destination
            ? $pipeline->then($destination)
            : $pipeline->thenReturn();
    }
}
