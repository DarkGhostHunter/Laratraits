<?php

namespace Tests;

use Exception;
use Illuminate\Support\Fluent;
use PHPUnit\Framework\TestCase;
use DarkGhostHunter\Laratraits\Comparable;

class ComparableTest extends TestCase
{
    /** @var \DarkGhostHunter\Laratraits\Comparable */
    protected $compared;

    protected function setUp() : void
    {
        parent::setUp();

        $this->compared = new class([
            'foo' => 'bar',
            'baz' => 'quz',
            'qux' => 'quuz'
        ]) extends Fluent
        {
            use Comparable;
        };
    }

    public function test_is_instance_of()
    {
        $this->assertTrue($this->compared->isAnyOf([Exception::class, Fluent::class]));
        $this->assertFalse($this->compared->isAnyOf([Exception::class]));
    }

    public function test_is_from_callback()
    {
        $result = $this->compared->isAnyOf(['foo', 'bar', 'quz'], static function ($compared, $comparable) {
            return $compared->foo === $comparable ? $comparable : null;
        });

        $this->assertSame('bar', $result);

        $result = $this->compared->isAnyOf(['foo', 'quuz', 'quz'], static function ($compared, $comparable) {
            return $compared->foo === $comparable ? $comparable : null;
        });

        $this->assertFalse($result);
    }

    public function test_returns_key()
    {
        $this->assertSame(1, $this->compared->isAnyOf([Exception::class, Fluent::class], null, true));
        $this->assertFalse($this->compared->isAnyOf([Exception::class], null, true));

        $result = $this->compared->isAnyOf(['foo', 'bar', 'quz'], static function ($compared, $comparable) {
            return $compared->foo === $comparable ? $comparable : null;
        }, true);

        $this->assertSame(1, $result);

        $result = $this->compared->isAnyOf(['foo', 'quuz', 'quz'], static function ($compared, $comparable) {
            return $compared->foo === $comparable ? $comparable : null;
        }, true);

        $this->assertFalse($result);
    }

    public function test_none_of_instance_of()
    {
        $this->assertFalse($this->compared->isNoneOf([Exception::class, Fluent::class]));
        $this->assertTrue($this->compared->isNoneOf([Exception::class]));
    }

    public function test_none_from_callback()
    {
        $result = $this->compared->isNoneOf(['foo', 'bar', 'quz'], static function ($compared, $comparable) {
            return $compared->foo === $comparable ? $comparable : null;
        });

        $this->assertFalse($result);

        $result = $this->compared->isNoneOf(['foo', 'quuz', 'quz'], static function ($compared, $comparable) {
            return $compared->foo === $comparable ? $comparable : null;
        });

        $this->assertTrue($result);
    }

    public function test_which_key()
    {
        $this->assertSame(1, $this->compared->whichOf([Exception::class, Fluent::class]));
        $this->assertFalse($this->compared->whichOf([Exception::class]));
    }

    public function test_which_key_from_callback()
    {
        $result = $this->compared->whichOf(['foo', 'bar', 'quz'], static function ($compared, $comparable) {
            return $compared->foo === $comparable ? $comparable : null;
        });

        $this->assertSame(1, $result);

        $result = $this->compared->whichOf(['foo', 'quuz', 'quz'], static function ($compared, $comparable) {
            return $compared->foo === $comparable ? $comparable : null;
        });

        $this->assertFalse($result);
    }
}