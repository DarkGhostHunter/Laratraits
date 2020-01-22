<?php
/**
 * Pipes Through
 *
 * This trait is fairly simple: takes the instance and sends it through a given Pipeline, instantiated by
 * the Service Container, and returns a result (hopefully the same instance). You can change the pipes,
 * the Closure to receive the result, the default methods to use, and even the Pipeline class itself.
 *
 * MIT License
 *
 * Copyright (c) Italo Israel Baeza Cabrera
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits;

use Closure;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;

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
