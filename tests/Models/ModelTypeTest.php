<?php

namespace Tests\Models;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use DarkGhostHunter\Laratraits\Models\ModelType;

class ModelTypeTest extends TestCase
{
    protected function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            Schema::create('foo', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('type');
                $table->timestamps();
            });

            Schema::create('bar', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('bar');
                $table->timestamps();
            });
        });

        parent::setUp();
    }

    public function testExtendedModelFiltersType()
    {
        ExtendedTypeTestModel::make()->forceFill([
            'name' => 'foo',
        ])->save();

        $models = ExtendedTypeTestModel::all();

        $this->assertCount(1, $models);
        $this->assertEquals('foo', $models->first()->name);
        $this->assertEquals('extended-type-test-model', $models->first()->type);
    }

    public function testExtendedModelOverridesDefaults()
    {
        CustomTypeTestModel::make()->forceFill([
            'name' => 'foo'
        ])->save();

        $models = CustomTypeTestModel::all();

        $this->assertCount(1, $models);
        $this->assertEquals('foo', $models->first()->name);
        $this->assertEquals('quz', $models->first()->bar);
    }
}


class BaseTestModel extends Model
{
    protected $table = 'foo';
}

class ExtendedTypeTestModel extends BaseTestModel
{
    use ModelType;
}

class CustomTypeTestModel extends BaseTestModel
{
    use ModelType;

    protected $table = 'bar';

    protected function getTypeQualifiedColumn()
    {
        return 'bar';
    }

    protected function getTypeName()
    {
        return 'quz';
    }
}
