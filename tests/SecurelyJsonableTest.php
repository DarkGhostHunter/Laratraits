<?php

namespace Tests;

use InvalidArgumentException;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\TestCase;
use DarkGhostHunter\Laratraits\SecurelyJsonable;

class SecurelyJsonableTest extends TestCase
{
    /** @var \Tests\TestJsonableClass */
    protected $class;

    protected function setUp() : void
    {
        parent::setUp();

        $this->class = new TestJsonableClass([
            'foo' => 'bar',
            'quz' => 'qux',
        ]);
    }

    public function test_serializes_and_unserializes_securely()
    {
        $serialized = json_encode($this->class);

        $instance = $this->class::fromJson($serialized);

        $this->assertInstanceOf(TestJsonableClass::class, $instance);
        $this->assertSame('bar', $this->class->foo);
        $this->assertSame('qux', $this->class->quz);
        $this->assertNull($this->class->signature);
        $this->assertSame('{"foo":"bar","quz":"qux","signature":"b3eeb0da04ebbc73ee17549d4354ce5de7e7445b3c82eeacc29c35f1d18b711b"}', $serialized);
    }

    public function test_unserialization_throws_exception_if_values_different()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->class::fromJson('{"foo":"bar","quz":"quz","signature":"b3eeb0da04ebbc73ee17549d4354ce5de7e7445b3c82eeacc29c35f1d18b711b"}');
    }

    public function test_unserialization_throws_exception_if_signature_invalid()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->class::fromJson('{"foo":"bar","quz":"qux","signature":"b3eeb0da04ebbc73ee17549d4354ce5de7e7445b3c82eeacc29c35f1d18b711c"}');
    }

    public function test_unserialization_throws_exception_if_signature_absent()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->class::fromJson('{"foo":"bar","quz":"qux"}');
    }
}

class TestJsonableClass extends Fluent
{
    use SecurelyJsonable;

    public function jsonSerialize() : array
    {
        return $this->addSignature(parent::jsonSerialize());
    }

    public static function fromJson(string $data)
    {
        return new static(static::checkSignature(json_decode($data, true)));
    }

    public function __serialize() : array
    {
        return $this->addSignature($this->attributes);
    }

    public function __unserialize(array $data) : void
    {
        $this->attributes = static::checkSignature($data);
    }

    protected static function hashKey() : string
    {
        return config('app.key');
    }

}