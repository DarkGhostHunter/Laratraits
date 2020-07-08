<?php

namespace Tests;

use Mockery;
use InvalidArgumentException;
use Illuminate\Support\Fluent;
use Orchestra\Testbench\TestCase;
use Illuminate\Hashing\HashManager;
use Illuminate\Contracts\Hashing\Hasher;
use DarkGhostHunter\Laratraits\SecurelySerializable;

class SecurelySerializableTest extends TestCase
{
    /** @var \Tests\TestSerializableClass */
    protected $class;

    protected function setUp() : void
    {
        parent::setUp();

        $this->class = new TestSerializableClass([
            'foo' => 'bar',
            'quz' => 'qux',
        ]);
    }

    public function test_serializes_and_unserializes_securely()
    {
        $serialized = serialize($this->class);

        $instance = unserialize($serialized);

        $this->assertInstanceOf(TestSerializableClass::class, $instance);
        $this->assertSame('bar', $this->class->foo);
        $this->assertSame('qux', $this->class->quz);
        $this->assertNull($this->class->signature);
    }

    public function test_serialization_includes_signature()
    {
        $hasher = Mockery::mock(Hasher::class);

        $this->swap('hash', $hasher);

        $hasher->shouldReceive('make')
            ->with(json_encode([
                'foo' => 'bar',
                'quz' => 'qux',
            ]))->andReturn('class_hash');

        $hasher->shouldReceive('check')
            ->with(json_encode([
                'foo' => 'bar',
                'quz' => 'qux',
            ]), 'class_hash')->andReturn(true);

        $this->swap('hash', $manager = Mockery::mock(HashManager::class));

        $manager->shouldReceive('driver')
            ->with(null)
            ->andReturn($hasher);

        $serialized = serialize($this->class);

        $this->assertStringContainsString('s:9:"signature";s:10:"class_hash"', $serialized);

        $instance = unserialize($serialized);

        $this->assertInstanceOf(TestSerializableClass::class, $instance);
        $this->assertSame('bar', $this->class->foo);
        $this->assertSame('qux', $this->class->quz);
    }

    public function test_unserialization_throws_exception_if_values_different()
    {
        $this->expectException(InvalidArgumentException::class);

        unserialize('O:27:"Tests\TestSerializableClass":3:{s:3:"foo";s:3:"bar";s:3:"quz";s:3:"quz";s:9:"signature";s:60:"$2y$10$eRG1AwLwV81BUgvZgswrUOXVXe5Jtr5ysNyOZ0ngOnrqBbycjSvuC";}');
    }

    public function test_unserialization_throws_exception_if_signature_invalid()
    {
        $this->expectException(InvalidArgumentException::class);

        unserialize('O:27:"Tests\TestSerializableClass":3:{s:3:"foo";s:3:"bar";s:3:"quz";s:3:"qux";s:9:"signature";s:60:"$2y$10$eRG1AwLwV81BUgvZgswrUOXVXe5Jtr5ysNyOZ0ngOnrqBbycjSvuP";}');
    }

    public function test_unserialization_throws_exception_if_signature_absent()
    {
        $this->expectException(InvalidArgumentException::class);

        unserialize('O:27:"Tests\TestSerializableClass":2:{s:3:"foo";s:3:"bar";s:3:"quz";s:3:"qux";}');
    }
}

class TestSerializableClass extends Fluent
{
    use SecurelySerializable;

    public function __serialize() : array
    {
        return $this->addSignature($this->attributes);
    }

    public function __unserialize(array $data) : void
    {
        $this->attributes = $this->checkSignature($data);
    }
}