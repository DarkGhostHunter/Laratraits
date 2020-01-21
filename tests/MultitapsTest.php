<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\Multitaps;

class MultitapsTest extends TestCase
{
    public function testMultitaps()
    {
        $multitapable = new class() {
            use Multitaps;

            public $foo;

            public function bar()
            {
                $this->foo = 'bar';
            }

            public function quz()
            {
                $this->foo = $this->foo === 'bar' ? 'quz' : 'bar';
            }

            public function qux()
            {
                $this->foo = $this->foo === 'quz' ? 'qux' : 'quz';
            }
        };

        $this->assertEquals('qux', $multitapable->multitap()->bar()->quz()->qux()->target->foo);
    }

    public function testMultitapsWithClosure()
    {
        $multitapable = new class() {
            use Multitaps;

            public $foo;

            public function qux()
            {
                $this->foo = $this->foo === 'quz' ? 'qux' : 'quz';
            }
        };

        $result = $multitapable->multitap(function ($tapable) {
            $tapable->foo = 'quz';
        })->qux()->target->foo;

        $this->assertEquals('qux', $result);
    }

    public function testMultitapsAndUntaps()
    {
        $multitapable = new class() {
            use Multitaps;

            public $foo;

            public function bar()
            {
                $this->foo = 'bar';
            }

            public function quz()
            {
                $this->foo = $this->foo === 'bar' ? 'quz' : 'bar';
            }
        };

        $result = $multitapable->multitap()->bar()->quzAndUntap();
        $this->assertNull($result);
        $this->assertEquals('quz', $multitapable->foo);
    }
}
