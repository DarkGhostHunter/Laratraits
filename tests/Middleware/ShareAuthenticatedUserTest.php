<?php

namespace Tests\Middleware;

use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\Factory;
use DarkGhostHunter\Laratraits\Middleware\ShareAuthenticatedUser;

class ShareAuthenticatedUserTest extends TestCase
{
    public function testSharesVerifiedUserInViews()
    {
        $controller = new class {

            public static $hits = false;

            public function show()
            {
                self::$hits = true;

                return 'foo';
            }
        };

        $this->app->instance('test-controller', $controller);

        Route::get('test', 'test-controller@show')->middleware(
            ShareAuthenticatedUser::class
        );

        Route::get('test-foo', 'test-controller@show')->middleware(
            ShareAuthenticatedUser::class . ':foo'
        );

        $view = app(Factory::class);

        $this->actingAs($user = (new User)->forceFill(['id' => 1]))->get('test');

        $this->assertArrayHasKey('authenticated', $view->getShared());
        $this->assertEquals($user, $view->getShared()['authenticated']);

        $this->actingAs($user = (new User)->forceFill(['id' => 2]))->get('test-foo');

        $this->assertArrayHasKey('foo', $view->getShared());
        $this->assertEquals($user, $view->getShared()['foo']);
    }
}
