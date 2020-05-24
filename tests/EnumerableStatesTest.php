<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\EnumerableStates;

class EnumerableStatesTest extends TestCase
{
    public function test_has_states_with_method()
    {
        $class = new class {
            use EnumerableStates;

            protected function states()
            {
                return ['foo', 'bar'];
            }
        };

        $this->assertNull($class->current());
        $this->assertInstanceOf(get_class($class), $class->assign('foo'));
        $this->assertEquals('foo', $class->current());
    }

    public function test_has_states_with_const()
    {
        $class = new class {
            use EnumerableStates;

            protected const STATES = ['foo', 'bar'];
        };

        $this->assertNull($class->current());
        $this->assertInstanceOf(get_class($class), $class->assign('foo'));
        $this->assertEquals('foo', $class->current());
    }

    public function test_exception_when_invalid_state()
    {
        $this->expectException(\LogicException::class);

        $class = new class {
            use EnumerableStates;

            protected const STATES = ['foo', 'bar'];
        };

        $class->assign('invalid');
    }

    public function test_initial_const_method()
    {
        $class = new class {
            use EnumerableStates;

            protected const STATES = ['foo', 'bar'];

            protected function initial()
            {
                return 'foo';
            }
        };

        $this->assertEquals('foo', $class->current());
    }

    public function test_initial_state_const()
    {
        $class = new class {
            use EnumerableStates;

            protected const STATES = ['foo', 'bar'];

            protected const STATE_INITIAL = 'foo';
        };

        $this->assertEquals('foo', $class->current());
    }

    public function test_exception_when_class_has_no_states()
    {
        $this->expectException(\LogicException::class);

        $class = new class {
            use EnumerableStates;
        };

        $class->assign('foo');
    }
}
