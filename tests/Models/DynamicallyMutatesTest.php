<?php

namespace Tests\Models;

use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use DarkGhostHunter\Laratraits\Models\DynamicallyMutates;

class DynamicallyMutatesTest extends TestCase
{
    public function testMutatesToType()
    {
        $model = new class extends Model {
            use DynamicallyMutates;

            public function __construct(array $attributes = [])
            {
                parent::__construct($attributes);

                $this->attributes = [
                    'int' => '1.24',
                    'integer' => '2.00',

                    'real' => '3.54',
                    'float' => '4.54',
                    'double' => '5.54',

                    'decimal:2' => '123.123123',

                    'string' => 2,

                    'bool' => '1',
                    'boolean' => '0',

                    'object' => json_encode(['foo' => 'bar', 'quz' => 'qux']),

                    'array' => json_encode(['foo' => 'bar', 'quz' => 'qux']),
                    'json' => json_encode(['foo' => 'bar', 'quz' => 'qux']),

                    'collection' => json_encode(['foo' => 'bar', 'quz' => 'qux']),

                    'date' => '2020-09-30',

                    'datetime' => '2020-09-30 14:52:63',
                    'custom_datetime' => '2020-09-31 14:52:63',

                    'timestamp' => '2020-09-31 14:52:63',

                    'null' => null,

                    'foo' => 'bar',

                    'value' => '',
                    'type' => '',
                ];
            }

            public function castTo($attribute) {
                $this->attributes['value'] = $this->attributes[$attribute];
                $this->attributes['type'] = $attribute;

                return $this->castAttributeInto('value', 'type');
            }
        };

        $this->assertIsInt($model->castTo('int'));
        $this->assertEquals(1, $model->castTo('int'));

        $this->assertIsInt($model->castTo('integer'));
        $this->assertEquals(2, $model->castTo('integer'));

        $this->assertIsFloat($model->castTo('real'));
        $this->assertEquals(3.54, $model->castTo('real'));
        $this->assertIsFloat($model->castTo('float'));
        $this->assertEquals(4.54, $model->castTo('float'));
        $this->assertIsFloat($model->castTo('double'));
        $this->assertEquals(5.54, $model->castTo('double'));

        $this->assertIsString($model->castTo('decimal:2'));
        $this->assertEquals('123.12', $model->castTo('decimal:2'));

        $this->assertIsString($model->castTo('string'));
        $this->assertEquals('2', $model->castTo('string'));

        $this->assertIsBool($model->castTo('bool'));
        $this->assertTrue($model->castTo('bool'));
        $this->assertIsBool($model->castTo('boolean'));
        $this->assertFalse($model->castTo('boolean'));

        $this->assertIsObject($model->castTo('object'));
        $this->assertEquals('bar', $model->castTo('object')->foo);
        $this->assertEquals('qux', $model->castTo('object')->quz);

        $this->assertIsArray($model->castTo('array'));
        $this->assertEquals('bar', $model->castTo('array')['foo']);
        $this->assertEquals('qux', $model->castTo('array')['quz']);

        $this->assertIsArray($model->castTo('json'));
        $this->assertEquals('bar', $model->castTo('json')['foo']);
        $this->assertEquals('qux', $model->castTo('json')['quz']);

        $this->assertInstanceOf(Collection::class, $model->castTo('collection'));
        $this->assertEquals('bar', $model->castTo('collection')['foo']);
        $this->assertEquals('qux', $model->castTo('collection')['quz']);

        $this->assertInstanceOf(Carbon::class, $model->castTo('date'));
        $this->assertEquals('2020-09-30', $model->castTo('date')->toDateString());

        $this->assertInstanceOf(Carbon::class, $model->castTo('datetime'));
        $this->assertEquals('2020-09-30 14:53:03', $model->castTo('datetime')->toDateTimeString());
        $this->assertInstanceOf(Carbon::class, $model->castTo('custom_datetime'));
        $this->assertEquals('2020-10-01 14:53:03', $model->castTo('custom_datetime')->toDateTimeString());

        $this->assertIsInt($model->castTo('timestamp'));
        $this->assertEquals(1601563983, $model->castTo('timestamp'));

        $this->assertNull($model->castTo('null'));

        $this->assertEquals('bar', $model->castTo('foo'));
    }
}
