<?php

namespace Tests\Eloquent;

use BadMethodCallException;
use Orchestra\Testbench\TestCase;
use Illuminate\Database\Eloquent\Model;
use DarkGhostHunter\Laratraits\Eloquent\FillsAttributes;

class FillsAttributesTest extends TestCase
{
    public function test_autofill_model()
    {
        $model = new class extends Model {
            use FillsAttributes;

            protected $attributes = [
                'quux' => 'ok',
            ];

            protected function autoFillable()
            {
                return ['foo', 'quz', 'quux'];
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
            'foo' => 'bar', 'quz' => 'qux', 'quux' => 'ok'
        ], $model->getAttributes());
    }

    public function test_autofill_model_with_property()
    {
        $model = new class extends Model {
            use FillsAttributes;

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

    public function test_autofill_fails_if_no_filler_is_set()
    {
        $this->expectException(BadMethodCallException::class);

        $model = new class extends Model {
            use FillsAttributes;

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
