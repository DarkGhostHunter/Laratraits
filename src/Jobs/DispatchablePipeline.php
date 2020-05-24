<?php
/**
 * Dispatchable Pipeline
 *
 * This class is in charge of holding the Pipeline and the "passable" thing, and execute the pipeline once
 * this Job is dispatched. This Job instance is used when you add the "DispatchesPipeline" trait in your
 * custom pipelines. Like all Jobs in Laravel, this is will return void once handled by the dispatcher.
 *
 * ---
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

namespace DarkGhostHunter\Laratraits\Jobs;


use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Pipeline\Pipeline;
use Illuminate\Foundation\Bus\Dispatchable;

class DispatchablePipeline implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Pipeline to execute.
     *
     * @var \Illuminate\Contracts\Pipeline\Pipeline
     */
    protected $pipeline;

    /**
     * Thing to send.
     *
     * @var mixed
     */
    protected $passable;

    /**
     * Create a new Queueable Pipeline instance.
     *
     * @param  \Illuminate\Contracts\Pipeline\Pipeline  $pipeline
     * @param  mixed $passable
     * @return void
     */
    public function __construct(Pipeline $pipeline, $passable)
    {
        $this->pipeline = $pipeline;
        $this->passable = $passable;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->pipeline->send($this->passable)->then(function ($passable) {
            return $passable;
        });
    }
}
