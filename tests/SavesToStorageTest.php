<?php

namespace DarkGhostHunter\Laratraits\Tests;

use Mockery;
use LogicException;
use JsonSerializable;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Filesystem\FilesystemAdapter;
use DarkGhostHunter\Laratraits\SavesToStorage;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

class SavesToStorageTest extends TestCase
{
    public function testSavesToStorage()
    {
        $storage = Storage::fake();

        $storable = new class() {
            use SavesToStorage;

            public function toStore()
            {
                return 'foo';
            }
        };

        $storable->saveToStore('test_path.json');

        $this->assertTrue($storage->exists('test_path.json'));
    }

    public function testSavesJsonable()
    {
        $storage = Storage::fake();

        $storable = new class() implements Jsonable {
            use SavesToStorage;

            /**
             * @inheritDoc
             */
            public function toJson($options = 0)
            {
                return '{"foo": "bar"}';
            }
        };

        $storable->saveToStore('test_path.json');

        $this->assertTrue($storage->exists('test_path.json'));
        $this->assertJson($storage->get('test_path.json'));
    }

    public function testSavesJsonSerializable()
    {
        $storage = Storage::fake();

        $storable = new class() implements JsonSerializable {
            use SavesToStorage;

            public function jsonSerialize()
            {
                return ['foo' => 'bar'];
            }
        };

        $storable->saveToStore('test_path.json');

        $this->assertTrue($storage->exists('test_path.json'));
        $this->assertJson($storage->get('test_path.json'));
    }

    public function testSavesHtmlable()
    {
        $storage = Storage::fake();

        $storable = new class() implements Htmlable {
            use SavesToStorage;

            public function toHtml()
            {
                return 'foo';
            }
        };

        $storable->saveToStore('test_path.json');

        $this->assertTrue($storage->exists('test_path.json'));
        $this->assertEquals('foo', $storage->get('test_path.json'));
    }

    public function testSavesStringable()
    {
        $storage = Storage::fake();

        $storable = new class() {
            use SavesToStorage;

            public function __toString()
            {
                return 'foo';
            }
        };

        $storable->saveToStore('test_path.json');

        $this->assertTrue($storage->exists('test_path.json'));
        $this->assertEquals('foo', $storage->get('test_path.json'));
    }

    public function testSavesObjectInstance()
    {
        $storage = $this->instance(FilesystemContract::class, Mockery::mock(FilesystemAdapter::class));

        $storable = new class() {
            use SavesToStorage;
        };

        $storage->shouldReceive('put')
            ->with($storable)
            ->andReturn('test_path.json');

        $storable->saveToStore('test_path.json');
    }

    public function testSavesWithDefaultStoragePath()
    {
        $storage = Storage::fake();

        $storable = new class() {
            use SavesToStorage;

            protected function defaultStoragePath() : string
            {
                return 'test_path.json';
            }

            public function __toString()
            {
                return 'foo';
            }
        };

        $storable->saveToStore();

        $this->assertTrue($storage->exists('test_path.json'));
        $this->assertEquals('foo', $storage->get('test_path.json'));
    }

    public function testExceptionWhenNoDefaultPath()
    {
        $this->expectException(LogicException::class);

        $storable = new class() {
            use SavesToStorage;
        };

        $storable->saveToStore();
    }
}
