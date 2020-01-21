<?php

namespace Tests;

use Mockery;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\DiscoverClasses;
use DarkGhostHunter\Laratraits\ClassDiscoverer;
use Illuminate\Contracts\Foundation\Application;

class DiscoverClassesTest extends TestCase
{
    public function testDiscoverClasses()
    {
        $discovers = new class() {
            use DiscoverClasses;
        };

        $app = Mockery::spy(Application::class);

        $app->shouldReceive('path')
            ->andReturn(realpath(__DIR__));
        $app->shouldReceive('basePath')
            ->andReturn(realpath(__DIR__ . '/..'));

        $this->app->when(ClassDiscoverer::class)
            ->needs(Application::class)
            ->give(function () use ($app) {
                return $app;
            });

        /** @var \Illuminate\Support\Collection $discovered */
        $discovered = $discovers->discover('Tests/Stubs');

        $this->assertCount(3, $discovered);
        $this->assertTrue($discovered->contains('Tests\Stubs\TestDiscoverableClassFoo'));
        $this->assertTrue($discovered->contains('Tests\Stubs\TestDiscoverableClassBar'));
        $this->assertTrue($discovered->contains('Tests\Stubs\TestDirectory\TestDiscoverableClassQuz'));

        $discovered = $discovers->discover('Tests/Stubs', 'foo');

        $this->assertCount(2, $discovered);
        $this->assertTrue($discovered->contains('Tests\Stubs\TestDiscoverableClassFoo'));
        $this->assertTrue($discovered->contains('Tests\Stubs\TestDirectory\TestDiscoverableClassQuz'));

        $discovered = $discovers->discover('Tests/Stubs', 'Tests\Stubs\TestInterface');

        $this->assertCount(2, $discovered);
        $this->assertTrue($discovered->contains('Tests\Stubs\TestDiscoverableClassFoo'));
        $this->assertTrue($discovered->contains('Tests\Stubs\TestDirectory\TestDiscoverableClassQuz'));
    }

    public function testExceptionWhenInterfaceDoesntExists()
    {
        $this->expectException(InvalidArgumentException::class);
        $discoverer = app(ClassDiscoverer::class)->path('tests');

        $discoverer->filterByInterface('invalid_interface');
    }

}
