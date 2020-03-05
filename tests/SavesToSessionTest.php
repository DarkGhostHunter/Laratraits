<?php

namespace DarkGhostHunter\Laratraits\Tests;

use Mockery;
use LogicException;
use JsonSerializable;
use Illuminate\Session\Store;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Htmlable;
use DarkGhostHunter\Laratraits\SavesToSession;
use Illuminate\Contracts\Session\Session as SessionContract;

class SavesToSessionTest extends TestCase
{
    public function testSavesToSession()
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

    public function testSavesJsonable()
    {
        $sessionable = new class() implements Jsonable {
            use SavesToSession;

            /**
             * @inheritDoc
             */
            public function toJson($options = 0)
            {
                return '{"foo":"bar"}';
            }
        };

        $sessionable->saveToSession('foo');

        $this->assertEquals('{"foo":"bar"}', Session::get('foo'));
    }

    public function testSavesJsonSerializable()
    {
        $sessionable = new class() implements JsonSerializable {
            use SavesToSession;

            public function jsonSerialize()
            {
                return ['foo' => 'bar'];
            }
        };

        $sessionable->saveToSession('foo');

        $this->assertEquals('{"foo":"bar"}', Session::get('foo'));
    }

    public function testSavesHtmlable()
    {
        $sessionable = new class() implements Htmlable {
            use SavesToSession;

            public function toHtml()
            {
                return 'bar';
            }
        };

        $sessionable->saveToSession('foo');

        $this->assertEquals('bar', Session::get('foo'));
    }

    public function testSavesStringable()
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

    public function testSavesObjectInstance()
    {
        $session = $this->instance(SessionContract::class, Mockery::mock(Store::class));

        $sessionable = new class() {
            use SavesToSession;
        };

        $session->shouldReceive('put')
            ->with('foo', $sessionable)
            ->andReturnUndefined();

        $sessionable->saveToSession('foo');
    }

    public function testSavesWithDefaultSessionKey()
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

    public function testExceptionWhenNoSessionKey()
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
