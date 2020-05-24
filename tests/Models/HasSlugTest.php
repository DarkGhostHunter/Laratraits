<?php

namespace Tests\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use DarkGhostHunter\Laratraits\Eloquent\HasSlug;

class HasSlugTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    protected function setUp() : void
    {
        $this->afterApplicationCreated(function () {

            Schema::create('foo', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('slug');
                $table->timestamps();
            });

            Schema::create('bar', function (Blueprint $table) {
                $table->increments('id');
                $table->string('quz');
                $table->string('qux');
                $table->timestamps();
            });
        });

        parent::setUp();
    }

    public function testRoutesBySlug()
    {
        Carbon::setTestNow($now = Carbon::create(2020, 1, 1));

        TestSlugableFooModel::create([
            'name' => $fooName = 'This Is A Test',
        ]);

        $this->app['router']->get('foo/{foo}', function (TestSlugableFooModel $foo) {
            return $foo;
        })->middleware('bindings');

        if (Str::startsWith(Application::VERSION, '7')) {
            $this->get('foo/this-is-a-test')->assertExactJson([
                'id' => 1,
                'name' => $fooName,
                'slug' => Str::slug($fooName),
                'created_at' => $now->toIso8601ZuluString('microseconds'),
                'updated_at' => $now->toIso8601ZuluString('microseconds'),
            ]);
        } else {
            $this->get('foo/this-is-a-test')->assertExactJson([
                'id' => 1,
                'name' => $fooName,
                'slug' => Str::slug($fooName),
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ]);
        }

        $this->get('foo/notfound')->assertNotFound();
    }

    public function testRoutesByPersonalizedSlug()
    {
        Carbon::setTestNow($now = Carbon::create(2020, 1, 1));

        TestSlugableBarModel::create([
            'quz' => $barName = 'What Happened?',
        ]);

        $this->app['router']->get('bar/{bar}', function (TestSlugableBarModel $bar) {
            return $bar;
        })->middleware('bindings');

        if (Str::startsWith(Application::VERSION, '7')) {
            $this->get('bar/what-happened')->assertExactJson([
                'id' => 1,
                'quz' => $barName,
                'qux' => Str::slug($barName),
                'created_at' => $now->toIso8601ZuluString('microseconds'),
                'updated_at' => $now->toIso8601ZuluString('microseconds'),
            ]);
        } else {
            $this->get('bar/what-happened')->assertExactJson([
                'id' => 1,
                'quz' => $barName,
                'qux' => Str::slug($barName),
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ]);
        }

        $this->get('bar/notfound')->assertNotFound();
    }
}

class TestSlugableFooModel extends Model
{
    use HasSlug;

    protected $table = 'foo';

    protected $fillable = ['name'];
}

class TestSlugableBarModel extends Model
{
    use HasSlug;

    protected $table = 'bar';

    protected $fillable = ['quz'];

    public function sluggableAttribute()
    {
        return 'quz';
    }

    protected function getSlugKey()
    {
        return 'qux';
    }
}
