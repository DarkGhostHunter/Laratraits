<?php

namespace Tests\Eloquent;

use LogicException;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use DarkGhostHunter\Laratraits\Eloquent\ModelType;

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

    public function test_extended_model_filters_type()
    {
        ExtendedTypeTestModel::make()->forceFill([
            'name' => 'foo',
        ])->save();

        $models = ExtendedTypeTestModel::all();

        $this->assertCount(1, $models);
        $this->assertEquals('foo', $models->first()->name);
        $this->assertEquals('extended_type_test_model', $models->first()->type);
    }

    public function test_extended_model_overrides_defaults()
    {
        CustomTypeTestModel::make()->forceFill([
            'name' => 'foo'
        ])->save();

        $models = CustomTypeTestModel::all();

        $this->assertCount(1, $models);
        $this->assertEquals('foo', $models->first()->name);
        $this->assertEquals('quz', $models->first()->bar);
    }

    public function test_exception_if_model_has_no_table_set()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The Tests\Eloquent\ExceptionCustomTypeTestModel model must set a common table name for all extending models.');

        new ExceptionCustomTypeTestModel;
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

    public function getModelTypeColumn()
    {
        return 'bar';
    }

    public function getModelType()
    {
        return 'quz';
    }
}

class ExceptionBaseTestModel extends Model
{
    use ModelType;

    public function getModelTypeColumn()
    {
        return 'bar';
    }

    public function getModelType()
    {
        return 'quz';
    }
}

class ExceptionCustomTypeTestModel extends ExceptionBaseTestModel
{

}
