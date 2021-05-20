<?php

namespace Tests\Eloquent;

use DarkGhostHunter\Laratraits\Eloquent\FromRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Orchestra\Testbench\TestCase;

class FromRequestTest extends TestCase
{
    /** @var \Illuminate\Database\Eloquent\Model */
    protected $class;

    protected function setUp(): void
    {
        $this->afterApplicationCreated(
            function () {
                Schema::create(
                    'foo',
                    function (Blueprint $table) {
                        $table->increments('id');
                        $table->string('bar');
                        $table->string('quz');
                        $table->timestamps();
                    }
                );
            }
        );

        $this->class = new class extends Model {
            use FromRequest;

            protected $table = 'foo';

            protected $fillable = ['bar', 'quz'];
        };

        parent::setUp();
    }

    public function test_creates_from_request()
    {
        $request = new Request();

        $request->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->createFrom(
            $request,
            ['bar' => 'required|string', 'quz' => 'required|string']
        );

        static::assertTrue($new->exists);
        static::assertEquals('foo', $new->bar);
        static::assertEquals('qux', $new->quz);
        static::assertNull($new->cougar);
    }

    public function test_creates_without_request()
    {
        $request = $this->swap('request', new Request());

        $request->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->createFrom(['bar' => 'required|string', 'quz' => 'required|string']);

        static::assertTrue($new->exists);
        static::assertEquals('foo', $new->bar);
        static::assertEquals('qux', $new->quz);
        static::assertNull($new->cougar);
    }

    public function test_exception_when_creating_rules_fail()
    {
        $this->expectException(ValidationException::class);

        $request = (new Request())->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $this->class->createFrom(
            $request,
            ['required' => 'required|string']
        );
    }

    public function test_makes_from_request()
    {
        $request = new Request();

        $request->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->makeFrom(
            $request,
            ['bar' => 'required|string', 'quz' => 'required|string']
        );

        static::assertFalse($new->exists);
        static::assertEquals('foo', $new->bar);
        static::assertEquals('qux', $new->quz);
        static::assertNull($new->cougar);
    }

    public function test_makes_without_request()
    {
        $request = $this->swap('request', new Request());

        $request->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->makeFrom(['bar' => 'required|string', 'quz' => 'required|string']);

        static::assertFalse($new->exists);
        static::assertEquals('foo', $new->bar);
        static::assertEquals('qux', $new->quz);
        static::assertNull($new->cougar);
    }

    public function test_exception_when_making_rules_fail()
    {
        $this->expectException(ValidationException::class);

        $request = (new Request())->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $this->class->makeFrom(
            $request,
            ['required' => 'required|string']
        );
    }

    public function test_fills_from_request()
    {
        $request = new Request();

        $request->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->fillFrom(
            $request,
            ['bar' => 'required|string', 'quz' => 'required|string']
        );

        static::assertFalse($new->exists);
        static::assertEquals('foo', $new->bar);
        static::assertEquals('qux', $new->quz);
        static::assertNull($new->cougar);
    }

    public function test_fills_without_request()
    {
        $request = $this->swap('request', new Request());

        $request->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->fillFrom(['bar' => 'required|string', 'quz' => 'required|string']);

        static::assertFalse($new->exists);
        static::assertEquals('foo', $new->bar);
        static::assertEquals('qux', $new->quz);
        static::assertNull($new->cougar);
    }

    public function test_exception_when_filling_rules_fail()
    {
        $this->expectException(ValidationException::class);

        $request = (new Request())->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $this->class->fillFrom(
            $request,
            ['required' => 'required|string']
        );
    }

    public function test_updates_from_request()
    {
        $request = new Request();

        $request->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->create(['bar' => 'bar', 'quz' => 'quz']);

        $new->updateFrom(
            $request,
            ['bar' => 'required|string', 'quz' => 'required|string']
        );

        static::assertTrue($new->exists);
        static::assertEquals('foo', $new->bar);
        static::assertEquals('qux', $new->quz);
        static::assertNull($new->cougar);
    }

    public function test_updates_without_request()
    {
        $request = $this->swap('request', new Request());

        $request->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->create(['bar' => 'bar', 'quz' => 'quz']);

        $new->updateFrom(['bar' => 'required|string', 'quz' => 'required|string']);

        static::assertTrue($new->exists);
        static::assertEquals('foo', $new->bar);
        static::assertEquals('qux', $new->quz);
        static::assertNull($new->cougar);
    }

    public function test_exception_when_updating_rules_fail()
    {
        $this->expectException(ValidationException::class);

        $request = (new Request())->replace(['bar' => 'foo', 'quz' => 'qux', 'cougar' => 'baz']);

        $new = $this->class->create(['bar' => 'bar', 'quz' => 'quz']);

        $new->updateFrom(
            $request,
            ['required' => 'required|string']
        );
    }
}
