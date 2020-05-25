<?php

namespace Tests\Eloquent\Casts;

use Orchestra\Testbench\TestCase;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use DarkGhostHunter\Laratraits\Eloquent\Casts\CastRepository;

class CastRepositoryTest extends TestCase
{
    protected $model;

    protected function setUp() : void
    {
        $this->afterApplicationCreated(function () {
            Schema::create('test', function (Blueprint $table) {
                $table->id();
                $table->json('castable')->nullable();
                $table->timestamps();
            });
        });

        $this->model = new class extends Model {
            protected $table = 'test';

            protected $casts = [
                'castable' => CastRepository::class,
            ];
        };

        parent::setUp();
    }

    public function test_casts_repository_from_array()
    {
        $this->model->setAttribute('castable', [
            'foo' => [
                'bar' => [
                    'quz',
                ],
                'qux' => [
                    'quuz',
                    'quux',
                ],
            ],
        ])->save();

        $instance = $this->model->find(1);

        $this->assertInstanceOf(Repository::class, $instance->castable);
        $this->assertSame(['quuz', 'quux'], $instance->castable->get('foo.qux'));
    }

    public function test_doesnt_casts_when_value_is_valid_json()
    {
        $this->model->setAttribute('castable', json_encode(['foo' => ['bar', 'quz']]))->save();

        $instance = $this->model->find(1);

        $this->assertInstanceOf(Repository::class, $instance->castable);
        $this->assertSame(['bar', 'quz'], $instance->castable->get('foo'));
    }

    public function test_casts_empty_value_into_empty_repository()
    {
        $this->model->save();

        $instance = $this->model->find(1);

        $this->assertInstanceOf(Repository::class, $instance->castable);
        $this->assertEmpty($instance->castable->all());
    }

    public function test_casts_simple_string_if_string_invalid()
    {
        $this->model->setAttribute('castable', 'INVALID')->save();

        $instance = $this->model->find(1);

        $this->assertInstanceOf(Repository::class, $instance->castable);
        $this->assertSame(['INVALID'], $instance->castable->all());
    }
}
