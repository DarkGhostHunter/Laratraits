<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\ThrottleMethods;

class ThrottlesMethodsTest extends TestCase
{
    public function test_throttles_class_method()
    {
        $class = new class {
            use ThrottleMethods;

            public static $executed = [];

            public function shouldThrottle()
            {
                static::$executed[] = true;
            }
        };

        $class->throttle(1, 1)->shouldThrottle();
        $class->throttle(1, 1)->shouldThrottle();

        $this->assertCount(1, $class::$executed);
    }

    public function test_uses_default()
    {
        $class = new class {
            use ThrottleMethods;

            public $used = false;

            public static $executed = [];

            public function shouldThrottle()
            {
                static::$executed[] = true;
            }
        };

        $class->throttle(1, 1, function ($class) {
            $class->used = true;
        })->shouldThrottle();

        $this->assertCount(1, $class::$executed);
        $this->assertFalse($class->used);

        $class->throttle(1, 1, function ($class) {
            $class->used = true;
        })->shouldThrottle();

        $this->assertCount(1, $class::$executed);
        $this->assertTrue($class->used);
    }

    public function test_clears_throttler()
    {
        $class = new class {
            use ThrottleMethods;

            public static $executed = [];

            public function shouldThrottle()
            {
                static::$executed[] = true;
            }
        };

        $class->throttle(1, 1)->shouldThrottle();

        $this->assertCount(1, $class::$executed);

        $class->throttleClear('shouldThrottle');

        $class->throttle(1, 1)->shouldThrottle();

        $this->assertCount(2, $class::$executed);
    }

    public function test_throttles_for_given_key()
    {
        $class = new class {
            use ThrottleMethods;

            public static $executed = [];

            public function shouldThrottle()
            {
                static::$executed[] = true;
            }
        };

        $class->for('foo')->throttle(1, 1)->shouldThrottle();

        $this->assertCount(1, $class::$executed);

        $class->throttle(1, 1)->shouldThrottle();

        $class->for('foo')->throttle(1, 1)->shouldThrottle();

        $this->assertCount(2, $class::$executed);

        $class->for('foo')->throttlerClear('shouldThrottle')->shouldThrottle();

        $this->assertCount(2, $class::$executed);

        $class->for('foo')->throttle(1, 1)->shouldThrottle();

        $this->assertCount(2, $class::$executed);
    }
}
