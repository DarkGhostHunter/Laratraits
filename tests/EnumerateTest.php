<?php

namespace Tests;

use LogicException;
use BadMethodCallException;
use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\Enumerate;

class EnumerateTest extends TestCase
{
    public function test_creates_enumerate()
    {
        $this->assertInstanceOf(Enumerate::class, Enumerate::from(['foo', 'bar']));
    }

    public function test_creates_enumerate_with_initial_value()
    {
        $enum = Enumerate::from(['foo', 'bar'], 'foo');

        $this->assertEquals('foo', $enum->current());
    }

    public function test_sets_states()
    {
        $enum = Enumerate::from($states = ['foo', 'bar', 'quz']);

        $this->assertEquals($states, $enum->states());
    }

    public function test_state_exists()
    {
        $enum = Enumerate::from(['foo', 'bar', 'quz']);

        $this->assertTrue($enum->has('foo'));
        $this->assertFalse($enum->has('doesnt_exists'));

        $enum = Enumerate::from($states = [
            'foo' => 10,
            'qux' => null
        ]);

        $this->assertTrue($enum->has('foo'));
        $this->assertTrue($enum->has('qux'));
    }

    public function test_is()
    {
        $enum = Enumerate::from(['foo', 'bar', 'quz']);

        $this->assertFalse($enum->is('foo'));
        $this->assertFalse($enum->is('bar'));

        $enum->foo();

        $this->assertTrue($enum->is('foo'));
        $this->assertFalse($enum->is('bar'));
    }

    public function test_current_state()
    {
        $enum = Enumerate::from(['foo', 'bar', 'quz']);

        $this->assertNull($enum->current());

        $enum->foo();

        $this->assertEquals('foo', $enum->current());
    }

    public function test_counts_states()
    {
        $enum = new Enumerate();

        $this->assertCount(0, $enum);

        $enum = Enumerate::from(['foo', 'bar', 'quz']);

        $this->assertCount(3, $enum);
    }

    public function test_dynamically_sets_states_and_returns_current_value()
    {
        $enum = Enumerate::from($states = ['foo', 'bar', 'quz']);

        foreach ($states as $state) {
            $this->assertInstanceOf(Enumerate::class, $enum->{$state}());
            $this->assertEquals($state, $enum->value());
        }
    }

    public function test_returns_map_value()
    {
        $enum = Enumerate::from($states = [
            'foo' => 10,
            'bar' => function() {return true; },
            'quz' => [],
            'qux' => null
        ]);

        $this->assertIsInt($enum->foo()->value());
        $this->assertIsCallable($enum->bar()->value());
        $this->assertIsArray($enum->quz()->value());
        $this->assertNull($enum->qux()->value());
    }

    public function test_exception_when_state_invalid()
    {
        $this->expectException(BadMethodCallException::class);

        $enum = Enumerate::from($states = ['foo', 'bar', 'quz']);

        $enum->invalid();
    }

    public function test_to_string()
    {
        $enum = Enumerate::from($states = ['foo', 'bar', 'quz']);

        $this->assertEquals('', (string) $enum);

        $enum->foo();

        $this->assertEquals('foo', (string) $enum);

        $enum = Enumerate::from($states = [
            'foo' => 10,
            'bar' => function() {return true; },
            'quz' => [],
            'qux' => null
        ]);

        $enum->bar();

        $this->assertEquals('bar', (string) $enum);
    }

    public function test_serializes_to_json()
    {
        $enum = Enumerate::from($states = ['foo', 'bar', 'quz']);

        $this->assertJson(json_encode($enum));
        $this->assertJson($enum->toJson());
        $this->assertEquals('null', json_encode($enum));
        $this->assertEquals('null', $enum->toJson());

        $enum->foo();

        $this->assertJson($enum->toJson());
        $this->assertEquals('"foo"', $enum->toJson());
        $this->assertJson($enum->toJson());
        $this->assertEquals('"foo"', $enum->toJson());
    }

    public function test_uses_initial_state()
    {
        $class = new class extends Enumerate {
            protected $current = 'foo';
            protected $states = ['foo', 'bar'];
        };

        $this->assertEquals('foo', $class->current());
    }

    public function test_exception_when_initial_state_is_invalid()
    {
        $this->expectException(LogicException::class);

        new class extends Enumerate {
            protected $current = 'invalid';
            protected $states = ['foo', 'bar'];
        };
    }

    public function test_sets_state()
    {
        $class = new class extends Enumerate {
            protected $current = 'foo';
            protected $states = ['foo', 'bar'];
        };

        $class->set('bar');

        $this->assertEquals('bar', $class->current());
    }

    public function test_exception_when_sets_invalid_state()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("The state [quz] doesn't exists in this Enumerate instance.");

        $class = new class extends Enumerate {
            protected $current = 'foo';
            protected $states = ['foo', 'bar'];
        };

        $class->set('quz');
    }
}
