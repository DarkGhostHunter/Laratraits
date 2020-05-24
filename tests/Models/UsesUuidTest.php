<?php

namespace Tests\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Collection;
use DarkGhostHunter\Laratraits\Eloquent\UsesUuid;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UsesUuidTest extends TestCase
{
    public function testFillsUuid()
    {
        $model = new class extends Model
        {
            use UsesUuid;
        };

        $this->assertInstanceOf(Uuid::class, $model->uuid);
    }

    public function testUuidScope()
    {
        Schema::create('test_table', function (Blueprint $blueprint) {
            $blueprint->increments('id');
            $blueprint->uuid('uuid');
            $blueprint->string('foo');
            $blueprint->string('bar');
            $blueprint->string('quz');
            $blueprint->string('qux');
            $blueprint->timestamps();
        });

        $uuids = [];

        for ($i = 0; $i < 10; ++$i) {
            DB::table('test_table')->insert([
                'uuid' => $uuids[] = Str::uuid(),
                'foo' => $i *2,
                'bar' => $i *3,
                'quz' => $i *4,
                'qux' => $i *5,
            ]);
        }

        $model = new class extends Model
        {
            use UsesUuid;

            protected $table = 'test_table';
        };

        $this->assertEquals((string)$uuids[1], $model->findUuid($uuids[1])->uuid);

        /** @var \Illuminate\Database\Eloquent\Collection $models */
        $models = $model->findManyUuid([$uuids[3], $uuids[5]]);

        $this->assertCount(2, $models);
        $this->assertEquals($uuids[3], $models->firstWhere('uuid', $uuids[3])->uuid);
        $this->assertEquals($uuids[5], $models->firstWhere('uuid', $uuids[5])->uuid);

        try {
            $model->findUuidOrFail('non_existant');
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(ModelNotFoundException::class, $exception);
            $this->assertEquals($exception->getModel(), get_class($model));
        }

        try {
            $model->findUuidOrFail(['non_existant', 'invalid']);
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(ModelNotFoundException::class, $exception);
            $this->assertEquals($exception->getModel(), get_class($model));
        }

        try {
            $model->findUuidOrFail([$uuids[7], 'non_existant', 'invalid']);
        } catch (\Throwable $exception) {
            $this->assertInstanceOf(ModelNotFoundException::class, $exception);
            $this->assertEquals($exception->getModel(), get_class($model));
        }

        $found = $model->findUuidOrFail([$uuids[7], $uuids[6]]);
        $this->assertCount(2, $found);

        $found = $model->findUuidOrFail($uuids[7]);
        $this->assertEquals((string)$uuids[7], $found->uuid);

        $new = $model->findManyUuid([]);
        $this->assertInstanceOf(Collection::class, $new);
        $this->assertCount(0, $new);

        $new = $model->findManyUuid(['non_existant', 'invalid']);
        $this->assertInstanceOf(Collection::class, $new);
        $this->assertCount(0, $new);

        $new = $model->findManyUuid(['non_existant', 'invalid', $uuids[7]]);
        $this->assertInstanceOf(Collection::class, $new);
        $this->assertCount(1, $new);

        $new = $model->findUuidOrNew($uuids[7]);

        $this->assertEquals((string)$uuids[7], $new->uuid);

        $new = $model->findUuidOrNew('non_existant');

        $this->assertEquals(get_class($model), get_class($new));
        $this->assertFalse($new->exists);

        $not = $model->whereUuid($uuids[1])->get();
        $this->assertCount(1, $not);

        $not = $model->whereUuid([$uuids[1], $uuids[8], 'invalid'])->get();
        $this->assertCount(2, $not);

        $not = $model->whereUuidNot($uuids[1])->get();
        $this->assertCount(9, $not);

        $not = $model->whereUuidNot([$uuids[1], $uuids[9], 'invalid'])->get();
        $this->assertCount(8, $not);
    }
}
