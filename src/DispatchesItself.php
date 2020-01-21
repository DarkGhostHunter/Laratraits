<?php

namespace DarkGhostHunter\Laratraits;

use Illuminate\Support\Str;

/**
 * Trait DispatchesItself
 * ---
 * This trait dispatches the object through a job instantly, without having to manually instance the Job
 * separately and configure it every time. You can use a default Job, or have multiple jobs separated
 * by a {nameJob} to allow more than one Job depending on what you dispatch to the application bus.
 *
 * @package DarkGhostHunter\Laratraits
 */
trait DispatchesItself
{
    /**
     * Dispatches the current instance to a default Job instance.
     *
     * @param  string|array  $parameters
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Foundation\Bus\PendingChain|mixed
     */
    public function dispatch(...$parameters)
    {
        return $this->defaultJob($parameters);
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
        return $this->{Str::camel($job . 'Job')}($parameters);
    }

    /**
     * Creates a Job instance with this object injected to it and some parameters.
     *
     * @param  array  $parameters
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Foundation\Bus\PendingChain|object
     */
    abstract protected function defaultJob(array $parameters);
}
