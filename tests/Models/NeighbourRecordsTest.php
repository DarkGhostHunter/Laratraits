<?php

namespace Tests\Models;

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
                $table->string('primary');
                $table->string('random');
                $table->timestamps();
            });

            $this->model = new class () extends Model {
                use NeighbourRecords;
                protected $table = 'foo';
                protected $primaryKey = 'primary';
                public $incrementing = false;
                protected $fillable = ['primary', 'random'];
            };

            foreach (['foo', 'bar', 'quz', 'qux'] as $item) {
                $this->model->create([
                    'primary' => $item,
                    'random'  => Str::random(16),
                ]);
            }
        });

        parent::setUp();
    }

    public function testNeighbourRecords()
    {
        $current = $this->model->find('bar');

        $this->assertEquals('foo', $current->prevRecord()->primary);
        $this->assertEquals('quz', $current->nextRecord()->primary);

        $current = $this->model->find('foo');

        $this->assertNull($current->prevRecord());
        $this->assertEquals('bar', $current->nextRecord()->primary);

        $current = $this->model->find('qux');

        $this->assertEquals('quz', $current->prevRecord()->primary);
        $this->assertNull($current->nextRecord());
    }
}
