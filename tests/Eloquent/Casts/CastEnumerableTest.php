<?php

namespace Tests\Eloquent\Casts;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use DarkGhostHunter\Laratraits\Eloquent\Casts\CastEnumerable;

class CastEnumerableTest extends TestCase
{
    protected $model;

    protected $enum;

    protected function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            Schema::create('test', function (Blueprint $table) {
                $table->id();
                $table->string('castable')->nullable();
                $table->timestamps();
            });
        });

        $this->model = new class extends Model {
            protected $table = 'test';

            protected $casts = ['castable' => TestCastEnumerable::class];
        };

        parent::setUp();
    }

    public function test_casts_enum()
    {
        $this->assertNull($this->model->castable->current());

        $this->model->castable = 'foo';

        $this->assertSame('foo', $this->model->castable->current());

        $this->model->save();

        $instance = $this->model->find(1);

        $this->assertInstanceOf(CastEnumerable::class, $instance->castable);
        $this->assertSame('foo', $instance->castable->current());
    }

    public function test_cast_enum_with_initial()
    {
        $this->model = new class extends Model {
            protected $table = 'test';

            protected $casts = ['castable' => TestCastEnumerableInitial::class];
        };

        $this->assertSame('foo', $this->model->castable->current());

        $this->model->castable = 'bar';

        $this->assertSame('bar', $this->model->castable->current());

        $this->model->save();

        $instance = $this->model->find(1);

        $this->assertInstanceOf(CastEnumerable::class, $instance->castable);
        $this->assertSame('bar', $instance->castable->current());
    }
}

class TestCastEnumerable extends CastEnumerable
{
    protected $states = ['foo', 'bar', 'quz'];
}

class TestCastEnumerableInitial extends CastEnumerable
{
    protected $states = ['foo', 'bar', 'quz'];
    protected $current = 'foo';
}
