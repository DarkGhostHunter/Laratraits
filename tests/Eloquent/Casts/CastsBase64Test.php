<?php

namespace Tests\Eloquent\Casts;

use DarkGhostHunter\Laratraits\Eloquent\Casts\CastsBase64;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use RuntimeException;

class CastsBase64Test extends TestCase
{
    /** @var \Illuminate\Database\Eloquent\Model */
    protected $model;

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

            protected $casts = [
                'castable' => CastsBase64::class,
            ];
        };

        parent::setUp();
    }

    public function test_sets_and_gets_base64()
    {
        $binary = random_bytes(10);

        $model = $this->model->newInstance();

        $model->castable = $binary;
        $model->save();

        $model = $this->model->newModelQuery()->find(1);

        $this->assertEquals($binary, $model->castable);
        $this->assertDatabaseHas('test', [
            'id' => 1,
            'castable' => 'base64:' . base64_encode($binary)
        ]);
    }

    public function test_adds_prefix_base64_when_doesnt_have_it()
    {
        $binary = random_bytes(10);

        DB::table('test')->insert(['id' => 1, 'castable' => base64_encode($binary)]);

        $model = $this->model->newModelQuery()->find(1);

        $model->castable = $binary;
        $model->save();

        $model = $this->model->newModelQuery()->find(1);

        $this->assertEquals($binary, $model->castable);
        $this->assertDatabaseHas('test', [
            'id' => 1,
            'castable' => 'base64:' . base64_encode($binary)
        ]);
    }

    public function test_exception_on_get_invalid_binary_data()
    {
        DB::table('test')->insert(['id' => 1, 'castable' => '平仮名,ひらがな']);
        $model = $this->model->newModelQuery()->findOrFail(1);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Failed decoding BASE64 column [castable] in model [' . get_class($model) . '].'
        );

        $model->castable;
    }
}
