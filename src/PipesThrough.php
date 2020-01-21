<?php

namespace DarkGhostHunter\Laratraits;

use Closure;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;

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
     * Pipes the current instance into a pipeline.
     *
     * @param  string|array  $pipes  The list of pipes (in order) to send this class instance
     * @param  \Closure|null  $destination  Optional closure that will receive the result
     * @return $this|mixed
     */
    public function pipe($pipes = null, Closure $destination = null)
    {
        $pipeline = $this->makePipeline()->send($this);

        if ($pipes) {
            $pipeline->through($pipes);
        }

        return $destination
            ? $pipeline->then($destination)
            : $pipeline->thenReturn();
    }

    /**
     * Instances the Pipeline
     *
     * @return \Illuminate\Contracts\Pipeline\Pipeline
     */
    protected function makePipeline() : PipelineContract
    {
        // By default we create the default Pipeline class, but if your pipes don't depend
        // on a Service Container, you can just instance the pipeline with an empty one.
        // If you need custom pipeline handling, you can extend the default pipeline.
        return app(Pipeline::class);
    }
}
