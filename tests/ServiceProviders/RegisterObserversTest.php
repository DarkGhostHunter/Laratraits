<?php

namespace Tests\ServiceProviders;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Events\Dispatcher;
use DarkGhostHunter\Laratraits\ServiceProviders\RegisterObservers;

class RegisterObserversTest extends TestCase
{
    public function test_register_observers()
    {
        $class = new class($this->app) extends ServiceProvider {
            use RegisterObservers;

            protected $observers = [
                'Tests\ServiceProviders\TestModelFoo' => 'Tests\ServiceProviders\TestFooObserver',
                'Tests\ServiceProviders\TestModelBar' => [
                    'Tests\ServiceProviders\TestBarObserver',
                    'Tests\ServiceProviders\TestQuzObserver',
                ],
            ];

            public function boot()
            {
                $this->registerObservers();
            }
        };

        $this->app->register($class);

        $dispatcher = app(Dispatcher::class);

        $this->assertNotEmpty($dispatcher->getListeners('eloquent.created: Tests\ServiceProviders\TestModelFoo'));
        $this->assertNotEmpty($dispatcher->getListeners('eloquent.saved: Tests\ServiceProviders\TestModelBar'));
        $this->assertNotEmpty($dispatcher->getListeners('eloquent.deleted: Tests\ServiceProviders\TestModelBar'));
    }
}

class TestModelFoo extends Model
{

}

class TestModelBar extends Model
{

}

class TestFooObserver
{
    public function created()
    {

    }
}

class TestBarObserver
{
    public function saved()
    {

    }
}

class TestQuzObserver
{
    public function deleted()
    {

    }
}
