<?php

namespace Tests\Eloquent;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use DarkGhostHunter\Laratraits\Eloquent\NeighbourRecords;

class NeighbourRecordsTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    protected function setUp() : void
    {
        $this->afterApplicationCreated(function () {

            Schema::create('foo', function (Blueprint $table) {
                $table->id();
                $table->string('primary');
                $table->string('random');
                $table->timestamps();
            });

            $this->model = new class () extends Model {
                use NeighbourRecords;
                protected $table = 'foo';
                protected $fillable = ['primary', 'random'];
            };

            $now = now();

            foreach (['foo', 'bar', 'quz', 'qux'] as $item) {
                Carbon::setTestNow($now->addSecond());
                $this->model->create([
                    'primary' => $item,
                    'random'  => Str::random(16),
                ]);
            }

            Carbon::setTestNow();
        });

        parent::setUp();
    }

    public function test_neighbour_records()
    {
        $current = $this->model->find(2);

        $this->assertEquals(1, $current->prevRecord()->getKey());
        $this->assertEquals(3, $current->nextRecord()->getKey());

        $current = $this->model->find(1);

        $this->assertNull($current->prevRecord());
        $this->assertEquals(2, $current->nextRecord()->getKey());

        $current = $this->model->find(4);

        $this->assertEquals(3, $current->prevRecord()->getKey());
        $this->assertNull($current->nextRecord());
    }

    public function test_filters_query()
    {
        Schema::create('bar', function (Blueprint $table) {
            $table->id();
            $table->string('primary');
            $table->string('random');
            $table->timestamps();
        });

        $this->model = new class () extends Model {
            use NeighbourRecords;
            protected $table = 'bar';
            protected $fillable = ['primary', 'random'];
            protected function filterNeighbourQuery($builder)
            {
                return $builder->where('random', 'group-bar');
            }
        };

        $now = now();

        foreach (['foo', 'bar', 'quz', 'qux'] as $item) {
            Carbon::setTestNow($now->addSecond());
            $this->model->create([
                'primary' => $item,
                'random'  => 'group-foo',
            ]);
        }

        foreach (['foo', 'bar', 'quz', 'qux'] as $item) {
            Carbon::setTestNow($now->addSecond());
            $this->model->create([
                'primary' => $item,
                'random'  => 'group-bar',
            ]);
        }

        Carbon::setTestNow();

        $current = $this->model->find(6);

        $this->assertEquals(5, $current->prevRecord()->getKey());
        $this->assertEquals(7, $current->nextRecord()->getKey());

        $current = $this->model->find(5);

        $this->assertNull($current->prevRecord());
        $this->assertEquals(6, $current->nextRecord()->getKey());

        $current = $this->model->find(8);

        $this->assertEquals(7, $current->prevRecord()->getKey());
        $this->assertNull($current->nextRecord());
    }
}
