<?php

namespace DarkGhostHunter\Laratraits\Tests;

use Mockery;
use LogicException;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use DarkGhostHunter\Laratraits\SavesToStorage;
use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;

class SavesToStorageTest extends TestCase
{
    public function test_saves_to_storage()
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

    public function test_saves_stringable()
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

    public function test_saves_object_instance()
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

    public function test_saves_with_default_storage_path()
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

    public function test_exception_when_no_default_path()
    {
        $this->expectException(LogicException::class);

        $storable = new class() {
            use SavesToStorage;
        };

        $storable->saveToStore();
    }
}
