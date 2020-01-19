<?php

namespace DarkGhostHunter\Laratraits;

use LogicException;

/**
 * Trait DispatchesItself
 * ---
 * This trait dispatches the object through a job instantly, without having to manually instance the Job
 * separately and configure it every time. You can use a default Job, or have multiple jobs separated
 * by a name, allowing multiple jobs depending on what you want to dispatch to the application bus.
 *
 * @package DarkGhostHunter\Laratraits
 */
trait DispatchesItself
{
    /**
     * Dispatches the current instance to a default job.
     *
     * @param  mixed  ...$parameters  Parameteres passed down to the job instancing.
     * @return \Illuminate\Foundation\Bus\PendingChain|\Illuminate\Foundation\Bus\PendingDispatch|mixed
     */
    public function dispatch(...$parameters)
    {
        return $this->dispatchTo('default', ...$parameters);
    }

    /**
     * Dispatches the current instance to a job instance.
     *
     * @param  string  $job  The Job name
     * @param  array  $parameters Any optional parameters to pass to the Job instance
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Foundation\Bus\PendingChain|mixed
     *
     * @throws \LogicException
     */
    public function dispatchTo(string $job = 'default', ...$parameters)
    {
        if (method_exists($this, $method = $job . 'Job')) {
            return $this->{$method}(...$parameters);
        }

        throw new LogicException("The method $method for the job $job does not exists");
    }

    /**
     * Creates a Job instance with this object injected to it and some parameters.
     *
     * @param  array  $parameters
     * @return \Illuminate\Foundation\Bus\PendingDispatch|\Illuminate\Foundation\Bus\PendingChain|object
     */
    protected function defaultJob(...$parameters)
    {
        throw new LogicException('The class ' . class_basename($this) . ' has not set a default dispatchable Job.');
    }
}
