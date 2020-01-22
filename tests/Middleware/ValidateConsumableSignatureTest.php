<?php

namespace Tests\Middleware;

use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Cache\Repository;
use DarkGhostHunter\Laratraits\Middleware\ValidateConsumableSignature;

class ValidateConsumableSignatureTest extends TestCase
{
    public function testSignatureOnlyUsedOnce()
    {
        $controller = new class {

            public static $hits = false;

            public function show()
            {
                self::$hits = true;

                return 'foo';
            }
        };

        $this->app->instance('test-controller', $controller);

        $cache = $this->mock(Repository::class);

        $cache->shouldReceive('has')
            ->once()
            ->with(\Mockery::type('string'))
            ->andReturnFalse();

        $cache->shouldReceive('put')
            ->once()
            ->with(\Mockery::type('string'), null, \Mockery::type(Carbon::class))
            ->andReturnTrue();

        Route::get('test', 'test-controller@show')->middleware(
            ValidateConsumableSignature::class
        )->name('test-get');

        $signature = URL::signedRoute('test-get', ['foo' => 'bar'], 60);

        $this->get($signature)->assertOk();
        $this->assertTrue($controller::$hits);

        $controller::$hits = false;

        $cache->shouldReceive('has')
            ->once()
            ->with(\Mockery::type('string'))
            ->andReturnTrue();

        $this->get($signature)->assertForbidden();
        $this->assertFalse($controller::$hits);
    }
}
