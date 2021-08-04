<?php

namespace Tests;

use DarkGhostHunter\Laratraits\CacheRegenerator;
use DarkGhostHunter\Laratraits\RegeneratesCache;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\TestCase;

class RegeneratesCacheTest extends TestCase
{
    /**
     * @var \Illuminate\Support\Fluent|\DarkGhostHunter\Laratraits\RegeneratesCache
     */
    protected $object;

    protected function setUp(): void
    {
        parent::setUp();

        $this->object = new RegenerableObjectTest(['foo' => 'bar']);
    }

    public function test_uses_custom_ttl(): void
    {
        $store = $this->mock(Repository::class);

        $store->shouldReceive('setMultiple')
            ->once()
            ->with(\Mockery::type('array'), 99)
            ->andReturnNull();

        $this->mock('cache')
            ->shouldReceive('store')
            ->once()
            ->withNoArgs()
            ->andReturn($store);

        $this->object->cache()->regenerate(99, true);
    }

    public function test_reuses_regenerator(): void
    {
        $store = cache()->store();

        $this->mock('cache')
            ->shouldReceive('store')
            ->once()
            ->withNoArgs()
            ->andReturn($store);

        $instance = $this->object->cache();

        static::assertEquals($instance, $this->object->cache());
    }

    public function test_caches_data_if_no_data_was_cached(): void
    {
        $this->object->cache()->regenerate();

        static::assertEquals(['foo' => 'bar'], cache()->get('cougar')->toArray());
    }

    public function test_caches_data_if_is_fresher(): void
    {
        Carbon::setTestNow($now = now());

        static::assertTrue($this->object->cache()->regenerate());

        Carbon::setTestNow($now->addSecond());

        $this->object->baz = 'quz';

        $this->object->cache()->invalidate();

        // The array store saves the object itself, so we need to "replace" the object for a string.
        cache()->set('cougar', 'serialized');

        static::assertTrue($this->object->cache()->regenerate());

        static::assertEquals(['foo' => 'bar', 'baz' => 'quz'], cache()->get('cougar')->toArray());
    }

    public function test_doesnt_caches_data_if_older(): void
    {
        Carbon::setTestNow($now = now());

        static::assertTrue($this->object->cache()->regenerate());

        Carbon::setTestNow($now->subSecond());

        $this->object->cache()->invalidate();

        cache()->set('cougar', 'serialized');

        static::assertFalse($this->object->cache()->regenerate());

        static::assertEquals('serialized', cache()->get('cougar'));
    }

    public function test_caches_data_if_older_and_forced(): void
    {
        Carbon::setTestNow($now = now());

        static::assertTrue($this->object->cache()->regenerate());

        Carbon::setTestNow($now->subSecond());

        $this->object->cache()->invalidate();

        cache()->set('cougar', 'serialized');

        static::assertTrue($this->object->cache()->regenerate(60, true));

        static::assertEquals($this->object, cache()->get('cougar'));
    }

    public function test_forgets_data(): void
    {
        cache()->set('cougar', 'foo');
        cache()->set('cougar:time', 'foo');

        $this->object->cache()->forget();

        static::assertNull(cache()->get('cougar'));
        static::assertNull(cache()->get('cougar:time'));
    }

    public function test_invalidates_data_and_forgets(): void
    {
        cache()->set('cougar', 'foo');
        cache()->set('cougar:time', 'foo');

        $this->object->cache()->invalidate(true);

        static::assertNull(cache()->get('cougar'));
        static::assertNull(cache()->get('cougar:time'));
    }

    public function test_uses_custom_time_key(): void
    {
        CacheRegenerator::$timeSuffix = 'something';

        $this->object->cache()->regenerate();

        static::assertNotNull(cache()->get('cougar'));
        static::assertNotNull(cache()->get('cougarsomething'));

        CacheRegenerator::$timeSuffix = ':time';
    }

    public function test_hides_cache_from_serialization(): void
    {
        $store = cache()->store();

        $this->mock('cache')
            ->shouldReceive('store')
            ->twice()
            ->withNoArgs()
            ->andReturn($store);

        $this->object->cache();

        $string = serialize($this->object);

        static::assertEquals(
            '4f3a32373a2254657374735c526567656e657261626c654f626a65637454657374223a323a7b733a31333a22002a0061747472696275746573223b613a313a7b733a333a22666f6f223b733a333a22626172223b7d733a31343a22002a00726567656e657261746f72223b4e3b7d',
            bin2hex($string)
        );

        $object = unserialize($string);

        $instance = $object->cache();

        $serialized = serialize($instance);

        static::assertEquals(bin2hex('N;'), bin2hex($serialized));

        static::assertNull(unserialize($serialized));
    }
}

class RegenerableObjectTest extends Fluent
{
    use RegeneratesCache;

    protected function defaultCache(): Repository
    {
        return cache()->store();
    }

    protected function defaultCacheKey(): string
    {
        return 'cougar';
    }
}
