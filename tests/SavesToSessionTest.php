<?php

namespace DarkGhostHunter\Laratraits\Tests;

use LogicException;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Session;
use DarkGhostHunter\Laratraits\SavesToSession;
use Illuminate\Contracts\Session\Session as SessionContract;

class SavesToSessionTest extends TestCase
{
    public function test_saves_to_session()
    {
        $sessionable = new class() {
            use SavesToSession;

            public function toSession()
            {
                return 'bar';
            }
        };

        $sessionable->saveToSession('foo');

        $this->assertEquals('bar', Session::get('foo'));
    }

    public function test_saves_stringable()
    {
        $sessionable = new class() {
            use SavesToSession;

            public function __toString()
            {
                return 'bar';
            }
        };

        $sessionable->saveToSession('foo');

        $this->assertEquals('bar', Session::get('foo'));
    }

    public function test_saves_object_instance()
    {
        $session = new class implements SessionContract {
            public static $used = false;
            public function getName(){}
            public function getId(){}
            public function setId($id){}
            public function start(){}
            public function save(){}
            public function all(){}
            public function exists($key){}
            public function has($key){}
            public function get($key, $default = null){}
            public function put($key, $value = null){
                self::$used = true;
            }
            public function token(){}
            public function remove($key){}
            public function forget($keys){}
            public function flush(){}
            public function migrate($destroy = false){}
            public function isStarted(){}
            public function previousUrl(){}
            public function setPreviousUrl($url){}
            public function getHandler(){}
            public function handlerNeedsRequest(){}
            public function setRequestOnHandler($request){}
        };

        $session = $this->app->instance('session', $session);

        $sessionable = new class() {
            use SavesToSession;
        };

        $sessionable->saveToSession('foo');

        $this->assertTrue($session::$used);
    }

    public function test_saves_with_default_session_key()
    {
        $sessionable = new class() {
            use SavesToSession;

            protected function defaultSessionKey()
            {
                return 'foo';
            }

            public function __toString()
            {
                return 'bar';
            }
        };

        $sessionable->saveToSession();

        $this->assertEquals('bar', Session::get('foo'));
    }

    public function test_exception_when_no_session_key()
    {
        $this->expectException(LogicException::class);

        $sessionable = new class() {
            use SavesToSession;
            public function __toString()
            {
                return 'bar';
            }
        };

        $sessionable->saveToSession();
    }
}
