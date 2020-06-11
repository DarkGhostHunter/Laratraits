<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Model;
use DarkGhostHunter\Laratraits\Eloquent\ConditionFill;

class ConditionFillTest extends TestCase
{
    public function test_fills_when_true()
    {
        $class = new class extends Model {
            use ConditionFill;
        };

        $class->fillWhen(true, 'foo');

        $this->assertTrue($class->foo);

        $class->fillWhen(function () {
            return true;
        }, 'foo');

        $this->assertTrue($class->foo);

        $class->fillWhen('bar', 'foo');

        $this->assertSame('bar', $class->foo);

        $class->fillWhen('bar', 'foo', 'quz');

        $this->assertSame('quz', $class->foo);
    }

    public function test_does_not_fill_when_false()
    {
        $class = new class extends Model {
            use ConditionFill;
        };

        $class->fillWhen(false, 'foo');

        $this->assertNull($class->foo);

        $class->fillWhen(function () {
            return false;
        }, 'foo');

        $this->assertNull($class->foo);

        $class->fillWhen('', 'foo');

        $this->assertNull($class->foo);

        $class->fillWhen('', 'foo', 'quz');

        $this->assertNull($class->foo);
    }

    public function test_fills_unless_false()
    {
        $class = new class extends Model {
            use ConditionFill;
        };

        $class->fillUnless(false, 'foo');

        $this->assertFalse($class->foo);

        $class->fillUnless(function () {
            return false;
        }, 'foo');

        $this->assertFalse($class->foo);

        $class->fillUnless('', 'foo');

        $this->assertSame('', $class->foo);

        $class->fillUnless('', 'foo', 'quz');

        $this->assertSame('quz', $class->foo);
    }

    public function test_does_not_fill_unless_true()
    {
        $class = new class extends Model {
            use ConditionFill;
        };

        $class->fillUnless(true, 'foo');

        $this->assertNull($class->foo);

        $class->fillUnless(function () {
            return true;
        }, 'foo');

        $this->assertNull($class->foo);

        $class->fillUnless('bar', 'foo');

        $this->assertNull($class->foo);

        $class->fillUnless('bar', 'foo', 'quz');

        $this->assertNull($class->foo);
    }
}