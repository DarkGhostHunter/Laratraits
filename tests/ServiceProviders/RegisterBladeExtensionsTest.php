<?php

namespace Tests\ServiceProviders;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use DarkGhostHunter\Laratraits\ServiceProviders\RegisterBladeExtensions;

class RegisterBladeExtensionsTest extends TestCase
{
    public function test_registers_blade_extensions()
    {
        $class = new class($this->app) extends ServiceProvider {
            use RegisterBladeExtensions;

            protected $directives = [
                'testDirective' => 'Tests\ServiceProviders\TestBladeHandler@testDirectiveHandle'
            ];

            protected $if = [
                'testIf' => 'Tests\ServiceProviders\TestBladeHandler@testIfHandle',
            ];

            protected $include = [
                'testInclude' => 'testInclude'
            ];

            public function boot()
            {
                $this->registerBladeExtensions();
            }
        };

        $this->app->register($class);

        /** @var \Illuminate\View\Compilers\BladeCompiler $compiler */
        $compiler = Blade::getFacadeRoot();

        $this->assertArrayHasKey('testDirective', $compiler->getCustomDirectives());
        $this->assertArrayHasKey('testIf', $compiler->getCustomDirectives());
        $this->assertArrayHasKey('unlesstestIf', $compiler->getCustomDirectives());
        $this->assertArrayHasKey('elsetestIf', $compiler->getCustomDirectives());
        $this->assertArrayHasKey('endtestIf', $compiler->getCustomDirectives());
        $this->assertArrayHasKey('testInclude', $compiler->getCustomDirectives());
    }
}

class TestBladeHandler
{
    public static function testDirectiveHandle()
    {
        return 'ok';
    }
    public static function testIfHandle()
    {
        return 'ok';
    }
}
