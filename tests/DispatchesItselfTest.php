<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Bus;
use DarkGhostHunter\Laratraits\DispatchesItself;
use Illuminate\Foundation\Console\QueuedCommand;

class DispatchesItselfTest extends TestCase
{
    public function testDispatchesItself()
    {
        $bus = Bus::fake();

        $job = $this->mock(QueuedCommand::class);

        $dispatchable = new class() {
            use DispatchesItself;

            protected function testJob(array $parameters)
            {
                return QueuedCommand::dispatchNow('test_job', $parameters);
            }

            protected function defaultJob(array $parameters)
            {
                return QueuedCommand::dispatchNow($parameters);
            }
        };

        $job->shouldReceive('dispatch')
            ->with(['foo', 'bar'])
            ->andReturnSelf();

        $job->shouldReceive('dispatch')
            ->with('test_job', ['foo', 'bar'])
            ->andReturnSelf();

        $dispatchable->dispatch('foo', 'bar');
        $dispatchable->dispatchTo('test', 'foo', 'bar');

        $bus->assertDispatched(QueuedCommand::class);
    }
}
