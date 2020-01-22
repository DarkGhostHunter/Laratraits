<?php

namespace Tests\Models;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use DarkGhostHunter\Laratraits\Models\DefaultColumns;

class DefaultColumnsTest extends TestCase
{
    public function testAddsScopes()
    {
        Schema::create('test_table', function (Blueprint $blueprint) {
        $blueprint->increments('id');
        $blueprint->string('foo');
        $blueprint->string('bar');
        $blueprint->string('quz');
        $blueprint->string('qux');
        $blueprint->timestamps();
    });

        for ($i = 0; $i < 10; ++$i) {
            DB::table('test_table')->insert([
                'foo' => $i *2,
                'bar' => $i *3,
                'quz' => $i *4,
                'qux' => $i *5,
            ]);
        }

        $model = new class extends Model {
            use DefaultColumns;

            protected $table = 'test_table';

            protected static $defaultColumns = ['bar', 'quz'];
        };

        /** @var \Illuminate\Database\Eloquent\Collection $models */
        $models = $model->all();

        $models->each(function ($model) {
            $this->assertNull($model->foo);
            $this->assertNull($model->qux);
            $this->assertNotNull($model->bar);
            $this->assertNotNull($model->quz);
        });
    }
}
