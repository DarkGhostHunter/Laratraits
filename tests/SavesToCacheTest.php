<?php

namespace Tests;

use Mockery;
use LogicException;
use Illuminate\Cache\Repository;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Cache;
use DarkGhostHunter\Laratraits\SavesToCache;
use Illuminate\Contracts\Cache\Repository as RepositoryContract;

class SavesToCacheTest extends TestCase
{
    public function test_saves_to_cache()
    {
        $cacheable = new class() {
            use SavesToCache;

            protected function toCache()
            {
                return 'bar';
            }
        };

        $cacheable->saveToCache('foo');

        $this->assertEquals('bar', Cache::get('foo'));
    }

    public function test_saves_with_default_key()
    {
        $cacheable = new class() {
            use SavesToCache;

            protected function defaultCacheKey()
            {
                return 'foo';
            }

            protected function toCache()
            {
                return 'bar';
            }
        };

        $cacheable->saveToCache();

        $this->assertEquals('bar', Cache::get('foo'));
    }

    public function test_saves_with_default_ttl()
    {
        $store = $this->instance(RepositoryContract::class, Mockery::mock(Repository::class));

        Cache::shouldReceive('store')
            ->withNoArgs()
            ->andReturn($store);

        $store->shouldReceive('put')
            ->with('foo', 'bar', 60)
            ->andReturnTrue();

        $cacheable = new class() {
            use SavesToCache;

            protected function toCache()
            {
                return 'bar';
            }
        };

        $this->assertTrue($cacheable->saveToCache('foo'));
    }

    public function test_saves_stringable()
    {
        $cacheable = new class() {
            use SavesToCache;

            public function __toString()
            {
                return 'bar';
            }
        };

        $cacheable->saveToCache('foo');

        $this->assertEquals('bar', Cache::get('foo'));
    }

    public function test_saves_object_instance()
    {
        $store = $this->instance(RepositoryContract::class, Mockery::mock(Repository::class));

        Cache::shouldReceive('store')
            ->withNoArgs()
            ->andReturn($store);

        $cacheable = new class() {
            use SavesToCache;
        };

        $store->shouldReceive('put')
            ->with('foo', $cacheable, 60)
            ->andReturnUndefined();

        $cacheable->saveToCache('foo');
    }

    public function test_exception_when_no_default_key()
    {
        $this->expectException(LogicException::class);

        $cacheable = new class() {
            use SavesToCache;
        };

        $cacheable->saveToCache();
    }
}
