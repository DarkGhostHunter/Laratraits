<?php

namespace Tests;

use Illuminate\Pipeline\Pipeline;
use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\PipesThrough;

class PipesThroughTest extends TestCase
{
    public function testPipesThroughDefaultPipelines()
    {
        $pipes = new class() {
            use PipesThrough;
            public $foo;
        };

        $pipe = function ($object, $next) {
            $object->foo = 'bar';
            return $next($object);
        };

        $this->assertEquals('bar', $pipes->pipe($pipe)->foo);
    }

    public function testPipesCustomPipeline()
    {
        $pipeline = new class() extends Pipeline {
            public function __construct(Container $container = null)
            {
                parent::__construct($container);

                $this->pipes[] = function ($object, $next) {
                    $object->foo = 'bar';
                    return $next($object);
                };
            }
        };

        $pipes = new class($pipeline)  {
            use PipesThrough;
            public $foo;
            public function __construct($pipeline)
            {
                $this->pipeline = $pipeline;
            }
            protected function makePipeline() : \Illuminate\Contracts\Pipeline\Pipeline
            {
                return $this->pipeline;
            }
        };

        $this->assertEquals('bar', $pipes->pipe()->foo);
    }

    public function testPipesToClosureDestination()
    {
        $pipes = new class() {
            use PipesThrough;
            public $foo;
        };

        $pipe = function ($object, $next) {
            $object->foo = 'bar';
            return $next($object);
        };

        $destination = function ($passable) {
            $passable->foo = 'quz';
            return $passable;
        };

        $this->assertEquals('quz', $pipes->pipe($pipe, $destination)->foo);
    }
}
