<?php

namespace Tests\ServiceProviders;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use DarkGhostHunter\Laratraits\ServiceProviders\RegisterGates;

class RegisterGatesTest extends TestCase
{
    public function test_register_gates()
    {
        $class = new class($this->app) extends ServiceProvider {
            use RegisterGates;

            protected $gates = [
                'view-dashboard' => 'Tests\ServiceProviders\TestGatesHandler@foo',
                'create-users'   => 'Tests\ServiceProviders\TestGatesHandler@bar',
            ];

            public function boot()
            {
                $this->registerGates();
            }
        };

        $this->app->register($class);

        /** @var \Illuminate\Contracts\Auth\Access\Gate $auth */
        $auth = Gate::getFacadeRoot();

        $this->assertArrayHasKey('view-dashboard', $auth->abilities());
        $this->assertArrayHasKey('create-users', $auth->abilities());
    }
}
