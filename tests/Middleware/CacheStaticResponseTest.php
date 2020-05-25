<?php

namespace Tests\Middleware;

use Mockery;
use Illuminate\Http\Request;
use Illuminate\Cache\Repository;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\HtmlString;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Cache\Factory;
use DarkGhostHunter\Laratraits\Middleware\CacheStaticResponse;
use Illuminate\Contracts\Cache\Repository as RepositoryContract;

class CacheStaticResponseTest extends TestCase
{
    public function test_caches_static_response()
    {
        $controller = new class {

            public static $hits = false;

            public function show()
            {
                self::$hits = true;

                return new HtmlString('<p>Foo</p>');
            }
        };

        $this->app->instance('test-controller', $controller);

        Route::get('test', 'test-controller@show')->middleware(
            CacheStaticResponse::class
        );

        $this->get('test');
        $this->assertTrue($controller::$hits);

        $controller::$hits = false;

        $this->get('test');
        $this->assertFalse($controller::$hits);
    }

    public function test_uses_different_ttl_and_store()
    {
        $controller = new class {

            public static $hits = false;

            public function show(Request $request)
            {
                self::$hits = true;

                return 'foo';
            }
        };

        $cache = $this->instance(Factory::class, Mockery::mock(CacheManager::class));
        $store = $this->instance(RepositoryContract::class, Mockery::mock(Repository::class));

        $cache->shouldReceive('store')
            ->twice()
            ->with('test-store')
            ->andReturn($store);

        $store->shouldReceive('get')
            ->with(Mockery::type('string'))
            ->andReturnFalse();

        $store->shouldReceive('put')
            ->with(Mockery::type('string'), 'foo', 99*60);

        $this->app->instance('test-controller', $controller);

        Route::get('test', 'test-controller@show')->middleware(
            CacheStaticResponse::class . ':99,test-store'
        );

        $this->get('test');
        $this->assertTrue($controller::$hits);
    }
}
