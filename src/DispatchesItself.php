<?php
/**
 * Dispatches Itself
 *
 * This trait dispatches the object through a job instantly, without having to manually instance the Job
 * separately and configure it every time. You can use a default Job, or have multiple jobs separated
 * by a {nameJob} to allow more than one Job depending on what you dispatch to the application bus.
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

use Illuminate\Support\Str;

trait DispatchesItself
{
    /**
     * Dispatches the current instance to a default Job instance.
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Foundation\Bus\PendingChain|mixed
     */
    public function dispatch()
    {
        return $this->defaultJob(...func_get_args());
    }

    /**
     * Dispatches this object to a non-default Job.
     *
     * @param  string  $job
     * @param  mixed  ...$parameters
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Foundation\Bus\PendingChain|mixed
     */
    public function dispatchTo(string $job, ...$parameters)
    {
        return $this->{Str::camel($job . 'Job')}(...$parameters);
    }

    /**
     * Creates a Job instance with this object injected to it and some parameters.
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Foundation\Bus\PendingChain|object
     */
    abstract protected function defaultJob();
}
