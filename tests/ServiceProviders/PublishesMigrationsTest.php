<?php

namespace Tests\ServiceProviders;

use DarkGhostHunter\Laratraits\ServiceProviders\PublishesMigrations;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use SplFileInfo;

class PublishesMigrationsTest extends TestCase
{
    public function test_loads_migrations()
    {
        Carbon::setTestNow($now = now());

        $file = $this->mock(Filesystem::class);
        $this->swap('files', $file);

        $file->shouldReceive('allFiles')
            ->withArgs(static function (string $path): bool {
                return Str::endsWith($path, '../database/migrations');
            })
            ->once()
            ->andReturn([
                $foo = new SplFileInfo('/p/test/0000_00_00_000000_create_foo_table.php'),
                $bar = new SplFileInfo('/p/test/0000_00_00_000000_create_bar_table.php'),
                $baz = new SplFileInfo('/p/test/0000_00_00_000000_create_baz_table.php'),
                new SplFileInfo('/p/test/invalid.php'),
                new SplFileInfo('/p/test/0000_00_00_000000_create_bar_table.txt'),
                new SplFileInfo('/p/test/1000_00_00_000001_create_baz_table.php'),
            ]);

        $class = new class($this->app) extends ServiceProvider {
            use PublishesMigrations;

            public function boot()
            {
                $this->publishMigrations();
            }
        };

        $this->app->register($class);

        static::assertCount(3, ServiceProvider::$publishGroups['migrations']);
        static::assertArrayHasKey($foo->getPathname(), ServiceProvider::$publishGroups['migrations']);
        static::assertArrayHasKey($bar->getPathname(), ServiceProvider::$publishGroups['migrations']);
        static::assertArrayHasKey($baz->getPathname(), ServiceProvider::$publishGroups['migrations']);

        static::assertEquals(
            $this->app->databasePath('migrations/' . $now->format('Y_m_d_His') . '_create_foo_table.php'),
            ServiceProvider::$publishGroups['migrations'][$foo->getPathname()]
        );

        static::assertEquals(
            $this->app->databasePath('migrations/' . $now->format('Y_m_d_His') . '_create_bar_table.php'),
            ServiceProvider::$publishGroups['migrations'][$bar->getPathname()]
        );

        static::assertEquals(
            $this->app->databasePath('migrations/' . $now->format('Y_m_d_His') . '_create_baz_table.php'),
            ServiceProvider::$publishGroups['migrations'][$baz->getPathname()]
        );
    }
}
