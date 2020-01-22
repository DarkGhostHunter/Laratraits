<?php

namespace Tests\Controllers;

use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use DarkGhostHunter\Laratraits\Controllers\ThrottlesRequests;

class ThrottlesRequestTest extends TestCase
{
    public function testRequestIsNotThrottled()
    {
        $controller = new class {
            use ThrottlesRequests;

            public static $key;

            public static $fired = false;

            public function testOk(Request $request)
            {
                $this->checkThrottle($request);

                static::$key = $this->throttleKey($request);

                $this->incrementAttempts($request);

                return 'ok';
            }

            public function testOkWithHit(Request $request)
            {
                $this->checkThrottle();

                static::$key = $this->throttleKey($request);

                $this->incrementAttempts($request);

                return 'ok';
            }

            public function fireThrottledEvent()
            {
                static::$fired = true;
            }
        };

        $this->instance('test-controller', $controller);

        Route::get('test-ok', 'test-controller@testOk');

        $this->get('test-ok')->assertSee('ok');

        Cache::put($controller::$key, 10, 60);
        $this->assertFalse($controller::$fired);

        $this->get('test-ok')->assertStatus(302);
        $this->assertTrue($controller::$fired);
    }
}
