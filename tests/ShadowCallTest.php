<?php

namespace Tests;

use DarkGhostHunter\Laratraits\ShadowCall;
use Illuminate\Support\Fluent;
use PHPUnit\Framework\TestCase;

class ShadowCallTest extends TestCase
{
    public function test_when_true(): void
    {
        $object = new TestFluent(['foo' => 'bar']);

        $object->callWhen(true)->offsetSet('foo', 'baz');

        static::assertSame('baz', $object->foo);
    }

    public function test_when_true_callback(): void
    {
        $object = new TestFluent(['foo' => 'bar']);

        $object->callWhen(static function (string $offset): bool {
            return $offset === 'foo';
        })->offsetSet('foo', 'baz');

        static::assertSame('baz', $object->foo);
    }

    public function test_when_false(): void
    {
        $object = new TestFluent(['foo' => 'bar']);

        $object->callWhen(false)->offsetSet('foo', 'baz');

        static::assertSame('bar', $object->foo);
    }

    public function test_when_false_callback(): void
    {
        $object = new TestFluent(['foo' => 'bar']);

        $object->callWhen(static function (string $offset): bool {
            return $offset !== 'foo';
        })->offsetSet('foo', 'baz');

        static::assertSame('bar', $object->foo);
    }

    public function test_unless_true(): void
    {
        $object = new TestFluent(['foo' => 'bar']);

        $object->callUnless(true)->offsetSet('foo', 'baz');

        static::assertSame('bar', $object->foo);
    }

    public function test_unless_true_callback(): void
    {
        $object = new TestFluent(['foo' => 'bar']);

        $object->callUnless(static function (string $offset): bool {
            return $offset === 'foo';
        })->offsetSet('foo', 'baz');

        static::assertSame('bar', $object->foo);
    }

    public function test_unless_false(): void
    {
        $object = new TestFluent(['foo' => 'bar']);

        $object->callUnless(false)->offsetSet('foo', 'baz');

        static::assertSame('baz', $object->foo);
    }

    public function test_unless_false_callback(): void
    {
        $object = new TestFluent(['foo' => 'bar']);

        $object->callUnless(static function (string $offset): bool {
            return $offset !== 'foo';
        })->offsetSet('foo', 'baz');

        static::assertSame('baz', $object->foo);
    }
}

class TestFluent extends Fluent
{
    use ShadowCall;
}
