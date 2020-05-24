<?php

namespace Tests\Models;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use DarkGhostHunter\Laratraits\Eloquent\SoftCachesAccessors;

class SoftCachesAccessorsTest extends TestCase
{
    public function testCachesAccessor()
    {
        $model = new class extends Model {
            use SoftCachesAccessors;

            public static $hits = 0;

            protected $cachedAccessors = ['json'];

            protected $attributes = [
                'json' => '{"foo":"bar","quz":"qux"}',
                'not' => 'cached'
            ];

            protected function getJsonAttribute($json)
            {
                ++static::$hits;

                return Collection::make(json_decode($json, true));
            }

            protected function getNotAttribute($value)
            {
                return 'yes';
            }
        };

        $this->assertEquals('yes', $model->not);

        $this->assertInstanceOf(Collection::class, $model->json);
        $this->assertEquals(1, $model::$hits);

        $model->json;

        $this->assertEquals(1, $model::$hits);

        $model->flushAccessorsCache();

        $model->json;

        $this->assertEquals(2, $model::$hits);

        $this->assertInstanceOf(
            Collection::class,$model->getAttributeWithoutCache('json')
        );
        $this->assertEquals(3, $model::$hits);

        $model->withoutMutatorCache(function ($model) {
            $model->json;
        });
        $this->assertEquals(4, $model::$hits);
    }
}
