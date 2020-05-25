<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Event;
use DarkGhostHunter\Laratraits\FiresItself;

class FiresItselfTest extends TestCase
{
    public function test_fires_itself()
    {
        $event = Event::fake();

        $class = new class ('notfired') {
            use FiresItself;

            public static $payload;

            public function __construct($payload)
            {
                static::$payload = $payload;
            }
        };

        call_user_func([get_class($class), 'fire'], 'fired');

        $event->assertDispatched(get_class($class), function ($event) {
            return 'fired' === $event::$payload;
        });
    }

    public function test_fires_halted()
    {
        $event = Event::fake();

        $class = new class ('notfired') {
            use FiresItself;

            public static $payload;

            public function __construct($payload)
            {
                static::$payload = $payload;
            }
        };

        call_user_func([get_class($class), 'fireHalted'], 'fired');

        $event->assertDispatched(get_class($class), function ($event, $payload, $halted = null) {
            return 'fired' === $event::$payload && $halted === true;
        });
    }
}
