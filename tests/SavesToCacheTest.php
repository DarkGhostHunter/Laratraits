<?php

namespace Tests;

use Mockery;
use LogicException;
use JsonSerializable;
use Illuminate\Cache\Repository;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Htmlable;
use DarkGhostHunter\Laratraits\SavesToCache;
use Illuminate\Contracts\Cache\Repository as RepositoryContract;

class SavesToCacheTest extends TestCase
{
    public function testSavesToCache()
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

    public function testSavesWithDefaultKey()
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

    public function testSavesWithDefaultTtl()
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

    public function testSavesJsonable()
    {
        $cacheable = new class() implements Jsonable {
            use SavesToCache;

            /**
             * @inheritDoc
             */
            public function toJson($options = 0)
            {
                return '{"foo":"bar"}';
            }
        };

        $cacheable->saveToCache('foo');

        $this->assertEquals('{"foo":"bar"}', Cache::get('foo'));
    }

    public function testSavesJsonSerializable()
    {
        $cacheable = new class() implements JsonSerializable {
            use SavesToCache;

            public function jsonSerialize()
            {
                return ['foo' => 'bar'];
            }
        };

        $cacheable->saveToCache('foo');

        $this->assertEquals('{"foo":"bar"}', Cache::get('foo'));
    }

    public function testSavesHtmlable()
    {
        $cacheable = new class() implements Htmlable {
            use SavesToCache;

            public function toHtml()
            {
                return 'bar';
            }
        };

        $cacheable->saveToCache('foo');

        $this->assertEquals('bar', Cache::get('foo'));
    }

    public function testSavesStringable()
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

    public function testSavesObjectInstance()
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

    public function testExceptionWhenNoDefaultKey()
    {
        $this->expectException(LogicException::class);

        $cacheable = new class() {
            use SavesToCache;
        };

        $cacheable->saveToCache();
    }
}
