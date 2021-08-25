<?php

namespace Tests;

use DarkGhostHunter\Laratraits\Throws;
use Exception;
use PHPUnit\Framework\TestCase;

class ThrowsTest extends TestCase
{
    public function test_throwable_when_true(): void
    {
        $this->expectException(TestThrowable::class);
        $this->expectExceptionMessage('foo');

        TestThrowable::when(true, 'foo');
    }

    public function test_throwable_when_false(): void
    {
        static::assertNull(TestThrowable::when(false, 'foo'));
    }

    public function test_throwable_unless_true(): void
    {
        static::assertNull(TestThrowable::unless(true, 'foo'));
    }

    public function test_throwable_unless_false(): void
    {
        $this->expectException(TestThrowable::class);
        $this->expectExceptionMessage('foo');

        TestThrowable::unless(false, 'foo');
    }
}

class TestThrowable extends Exception
{
    use Throws;
}
