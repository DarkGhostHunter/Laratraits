<?php

namespace DarkGhostHunter\Laratraits\Pipelines;

trait DispatchesPipeline
{
    /**
     * Dispatches the Pipeline.
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function dispatchPipeline()
    {
        return DispatchablePipeline::dispatch($this, $this->getPassable());
    }

    /**
     * Dispatches the Pipeline now.
     *
     * @return $this
     */
    public function dispatchPipelineNow()
    {
        return DispatchablePipeline::dispatchNow($this, $this->getPassable());
    }

    /**
     * Returns the passable
     *
     * @return mixed
     */
    abstract public function getPassable();
}
