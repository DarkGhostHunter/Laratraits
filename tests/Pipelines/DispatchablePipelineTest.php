<?php

namespace Tests\Pipelines;

use Orchestra\Testbench\TestCase;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Bus\PendingDispatch;
use DarkGhostHunter\Laratraits\Pipelines\DispatchesPipeline;
use DarkGhostHunter\Laratraits\Pipelines\DispatchablePipeline;

class DispatchablePipelineTest extends TestCase
{
    public function testQueuesPipeline()
    {
        $this->assertEquals('bar', TestQueueablePipeline::$thing);

        $bus = Bus::fake();

        $pipeline = new TestQueueablePipeline;

        $this->assertInstanceOf(PendingDispatch::class, $pipeline->dispatchPipeline());

        $bus->assertDispatched(DispatchablePipeline::class);

        $this->assertEquals('quz', TestQueueablePipeline::$thing);
    }

    public function testQueuePipelineNow()
    {
        $this->assertEquals('bar', TestQueueablePipeline::$thing);

        $bus = Bus::fake();

        $pipeline = new TestQueueablePipeline;

        $this->assertNull($pipeline->dispatchPipelineNow());

        $bus->assertDispatched(DispatchablePipeline::class);

        $this->assertEquals('quz', TestQueueablePipeline::$thing);
    }

    protected function tearDown() : void
    {
        parent::tearDown();

        TestQueueablePipeline::$thing = 'bar';
    }
}

class TestQueueablePipeline extends Pipeline
{
    use DispatchesPipeline;

    public static $thing = 'bar';

    /**
     * @inheritDoc
     */
    public function getPassable()
    {
        static::$thing = 'quz';
        return 'foo';
    }
}
