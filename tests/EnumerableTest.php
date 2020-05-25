<?php

namespace Tests;

use ArrayIterator;
use LogicException;
use IteratorAggregate;
use BadMethodCallException;
use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\Enumerable;

class EnumerableTest extends TestCase
{
    public function test_creates_enumerable_from_list()
    {
        $list = ['foo', 'bar', 'quz'];
        $enum = new Enumerable($list);
        $this->assertEquals($list, $enum->states());

        $list = 'foo,bar,quz';
        $enum = new Enumerable($list);
        $this->assertEquals(['foo', 'bar', 'quz'], $enum->states());

        $list = ['foo', 'bar', 'quz'];
        $enum = Enumerable::from($list);
        $this->assertEquals($list, $enum->states());

        $list = 'foo,bar,quz';
        $enum = Enumerable::from($list);
        $this->assertEquals(['foo', 'bar', 'quz'], $enum->states());
    }

    public function test_exception_when_creates_enumerable_with_empty_state_list()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The DarkGhostHunter\Laratraits\Enumerable does not have states to set');

        new Enumerable();
    }

    public function test_creates_enumerable_from_list_and_initial_state()
    {
        $list = ['foo', 'bar', 'quz'];
        $enum = Enumerable::from($list, 'foo');

        $this->assertEquals('foo', $enum->current());
    }

    public function test_creates_custom_enumerable_from_initial_state()
    {
        $enum = new class extends Enumerable {
            protected $states = ['foo', 'bar', 'quz'];
        };

        $bar = call_user_func([$enum, 'as'], 'foo');

        $this->assertEquals('foo', $bar->current());

        $enum = new class extends Enumerable {
            protected $current = 'bar';
            protected $states = ['foo', 'bar', 'quz'];
        };

        $this->assertEquals('bar', $enum->current());
    }

    public function test_receives_states_as_iterable()
    {
        $states = new class implements IteratorAggregate {
            public function getIterator()
            {
                return new ArrayIterator(['foo', 'bar', 'quz']);
            }
        };

        $enum = new Enumerable($states);

        $this->assertEquals(['foo', 'bar', 'quz'], $enum->states());

        $enum = new Enumerable(new ArrayIterator(['foo', 'bar', 'quz']));

        $this->assertEquals(['foo', 'bar', 'quz'], $enum->states());
    }

    public function test_returns_current_state()
    {
        $states = ['foo', 'bar', 'quz'];
        $enum = new Enumerable($states);

        $enum->assign('foo');

        $this->assertTrue($enum->is('foo'));
        $this->assertFalse($enum->isNot('foo'));
        $this->assertFalse($enum->is('bar'));
        $this->assertTrue($enum->isNot('bar'));

        $enum->bar();

        $this->assertTrue($enum->is('bar'));
        $this->assertFalse($enum->isNot('bar'));
        $this->assertFalse($enum->is('foo'));
        $this->assertTrue($enum->isNot('foo'));
    }

    public function test_returns_current_state_as_null_when_uninitialized()
    {
        $states = ['foo', 'bar', 'quz'];

        $enum = new Enumerable($states);

        $this->assertNull($enum->current());
    }

    public function test_exception_when_sets_invalid_state()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The state \'qux\' doesn\'t exists in this Enumerate instance.');

        $enum = new Enumerable(['foo', 'bar', 'quz']);

        $enum->assign('qux');
    }

    public function test_sets_state_programmatically_when_true()
    {
        $enum = new Enumerable(['foo', 'bar', 'quz']);

        $enum->when(true, 'bar');

        $this->assertTrue($enum->is('bar'));

        $enum->when(function () {
            return true;
        }, 'quz');

        $this->assertTrue($enum->is('quz'));

        $enum->when(false, 'bar');

        $this->assertTrue($enum->is('quz'));

        $enum->when(function () {
            return false;
        }, 'bar');

        $this->assertTrue($enum->is('quz'));


    }

    public function test_sets_state_programmatically_when_false()
    {
        $enum = new Enumerable(['foo', 'bar', 'quz']);

        $enum->assign('foo');

        $enum->unless(true, 'bar');

        $this->assertTrue($enum->is('foo'));

        $enum->unless(function () {
            return true;
        }, 'quz');

        $this->assertTrue($enum->is('foo'));

        $enum->unless(false, 'bar');

        $this->assertTrue($enum->is('bar'));

        $enum->unless(function () {
            return false;
        }, 'quz');

        $this->assertTrue($enum->is('quz'));
    }

    public function test_counts_states()
    {
        $enum = new Enumerable(['foo', 'bar', 'quz']);

        $this->assertCount(3, $enum);
    }

    public function test_serializes_current_state_to_string()
    {
        $enum = Enumerable::from(['foo', 'bar', 'quz'], 'bar');

        $this->assertEquals('bar', (string)$enum);
        $this->assertEquals('bar', $enum->__toString());
    }

    public function test_serializes_null_state_to_empty_string()
    {
        $enum = Enumerable::from(['foo', 'bar', 'quz']);

        $this->assertEquals('', (string)$enum);
        $this->assertEquals('', $enum->__toString());
    }

    public function test_sets_enumerable_as_method()
    {
        $enum = Enumerable::from(['foo', 'bar', 'quz']);

        $enum->foo()->bar();

        $this->assertEquals('bar', $enum->current());
    }

    public function test_exception_when_state_call_doesnt_exists()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Call to undefined method DarkGhostHunter\Laratraits\Enumerable::qux()');

        $enum = Enumerable::from(['foo', 'bar', 'quz']);

        $enum->qux();
    }
}

