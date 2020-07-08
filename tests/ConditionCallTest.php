<?php

namespace Tests;

use Illuminate\Support\Fluent;
use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\ConditionCalls;

class ConditionCallTest extends TestCase
{
    /**
     * @var \stdClass & \DarkGhostHunter\Laratraits\ConditionCalls
     */
    protected $object;

    protected function setUp() : void
    {
        $this->object = new class()
        {
            use ConditionCalls;

            public $foo = 'bar';
        };

        parent::setUp();
    }

    public function test_calls_value()
    {
        $this->object->when('quz', function ($instance, $value) {
            $instance->foo = $value;
        });

        $this->assertSame('quz', $this->object->foo);
    }

    public function test_calls_callable_when_truthy()
    {
        $this->object->when('quz', function ($instance, $value) {
            $instance->foo = $value;
        });

        $this->assertSame('quz', $this->object->foo);
    }

    public function test_does_not_calls_callable_when_falsy()
    {
        $this->object->when('', function ($instance, $value) {
            $instance->foo = $value;
        });

        $this->assertSame('bar', $this->object->foo);
    }

    public function test_doesnt_calls_default_when_thrughy()
    {
        $this->object->when('qux', function ($instance, $value) {
            $instance->foo = $value;
        }, function ($instance, $value) {
            $instance->foo = $value . ' quz';
        });

        $this->assertSame('qux', $this->object->foo);
    }

    public function test_calls_default_when_falsy()
    {
        $this->object->when('', function ($instance, $value) {
            $instance->foo = $value;
        }, function ($instance, $value) {
            $instance->foo = $value . ' quz';
        });

        $this->assertSame(' quz', $this->object->foo);
    }

    public function test_calls_callable_unless_falsy()
    {
        $this->object->unless('', function ($instance, $value) {
            $instance->foo = $value . ' quz';
        });

        $this->assertSame(' quz', $this->object->foo);
    }

    public function test_does_not_calls_callable_unless_truthy()
    {
        $this->object->unless('quz', function ($instance, $value) {
            $instance->foo = $value;
        });

        $this->assertSame('bar', $this->object->foo);
    }

    public function test_doesnt_calls_default_when_falsy()
    {
        $this->object->unless(false, function ($instance, $value) {
            $instance->foo = $value;
        }, function ($instance, $value) {
            $instance->foo = $value . ' qux';
        });

        $this->assertFalse($this->object->foo);
    }

    public function test_calls_default_unless_truthy()
    {
        $this->object->unless('quz', function ($instance, $value) {
            $instance->foo = $value;
        }, function ($instance, $value) {
            $instance->foo = $value . ' qux';
        });

        $this->assertSame('quz qux', $this->object->foo);
    }

    public function test_class_callable_on_value_result()
    {
        $this->object->when(function () {
            return 'quz';
        }, function ($instance, $value) {
            $instance->foo = $value . ' bar';
        });

        $this->assertSame('quz bar', $this->object->foo);

        $this->object->unless(function () {
            return false;
        }, function ($instance, $value) {
            $instance->foo = (int)$value . ' bar';
        });

        $this->assertSame('0 bar', $this->object->foo);
    }

    public function test_class_callable_fluently_when_value_is_true()
    {
        $object = new class([
            'foo' => 'bar',
            'quz' => 'qux'
        ]) extends Fluent {
            use ConditionCalls;

            public function set($key, $value)
            {
                $this->attributes[$key] = $value;

                return $this;
            }
        };

        $object->when(true)->set('foo','cougar')->set('quz', 'foo');

        $this->assertSame('cougar', $object->foo);
        $this->assertSame('foo', $object->quz);

        $object->when(false)->set('foo','cougar')->set('quz', 'bar');

        $this->assertSame('cougar', $object->foo);
        $this->assertSame('bar', $object->quz);
    }

    public function test_class_callable_fluently_unless_value_is_false()
    {
        $object = new class([
            'foo' => 'bar',
            'quz' => 'qux'
        ]) extends Fluent {
            use ConditionCalls;

            public function set($key, $value)
            {
                $this->attributes[$key] = $value;

                return $this;
            }
        };

        $object->unless(false)->set('foo','cougar');

        $this->assertSame('cougar', $object->foo);

        $object->unless(true)->set('foo','cougar');

        $this->assertSame('cougar', $object->foo);
    }
}