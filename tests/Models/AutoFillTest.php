<?php

namespace Tests\Models;

use BadMethodCallException;
use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Model;
use DarkGhostHunter\Laratraits\Models\AutoFill;

class AutoFillTest extends TestCase
{
    public function testAutofillModel()
    {
        $model = new class extends Model {
            use AutoFill;

            protected function autoFillable()
            {
                return ['foo', 'quz'];
            }

            protected function fillFooAttribute()
            {
                return 'bar';
            }

            protected function fillQuzAttribute()
            {
                $this->attributes['quz'] = 'qux';
            }

            protected function fillNotAttribute()
            {
                $this->attributes['not'] = false;
            }
        };

        $this->assertEquals([
            'foo' => 'bar', 'quz' => 'qux'
        ], $model->getAttributes());
    }

    public function testAutofillModelWithProperty()
    {
        $model = new class extends Model {
            use AutoFill;

            protected $autoFillable = ['foo', 'quz'];

            protected function fillFooAttribute()
            {
                return 'bar';
            }

            protected function fillQuzAttribute()
            {
                $this->attributes['quz'] = 'qux';
            }

            protected function fillNotAttribute()
            {
                $this->attributes['not'] = false;
            }
        };

        $this->assertEquals([
            'foo' => 'bar', 'quz' => 'qux'
        ], $model->getAttributes());
    }

    public function testAutofillFailsIfNoFillerIsSet()
    {
        $this->expectException(BadMethodCallException::class);

        $model = new class extends Model {
            use AutoFill;

            protected $autoFillable = ['bar', 'quz'];

            protected function fillFooAttribute()
            {
                return 'bar';
            }

            protected function fillQuzAttribute()
            {
                $this->attributes['quz'] = 'qux';
            }

            protected function fillNotAttribute()
            {
                $this->attributes['not'] = false;
            }
        };
    }


}
