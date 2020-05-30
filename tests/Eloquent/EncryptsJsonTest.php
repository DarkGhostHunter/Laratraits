<?php

namespace Tests\Eloquent;

use JsonException;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Model;
use DarkGhostHunter\Laratraits\Eloquent\EncryptsJson;
use Illuminate\Contracts\Encryption\DecryptException;

class EncryptsJsonTest extends TestCase
{
    public function test_encrypts_to_json()
    {
        $class = new class([
            'foo' => 'bar',
            'quz' => 'qux',
        ]) extends Model {
            use EncryptsJson;

            protected $fillable = ['foo', 'quz'];
        };

        $encrypted = $class->toJson();

        $this->assertNotSame('{"foo":"bar","quz":"qux"}', $encrypted);

        $instance = $class::fromEncryptedJson($encrypted);

        $this->assertSame(['foo' => 'bar', 'quz' => 'qux'], $instance->attributesToArray());
    }

    public function test_prepares_model_after_decryption()
    {
        $class = new class([
            'foo' => 'bar',
            'quz' => 'qux',
        ]) extends Model {
            use EncryptsJson;

            protected $fillable = ['foo', 'quz'];

            public function afterJsonDecryption()
            {
                $this->attributes['quuz'] = 'quux';
            }
        };

        $encrypted = $class->toJson();

        $instance = $class::fromEncryptedJson($encrypted);

        $this->assertSame(['foo' => 'bar', 'quz' => 'qux', 'quuz' => 'quux'], $instance->attributesToArray());
    }

    public function test_exception_when_invalid_json()
    {
        if (PHP_VERSION_ID < 70300) {
            $this->markTestSkipped('JSON Exceptions are not enforceable below PHP 7.3');
        }

        $class = new class([
            'foo' => 'bar',
            'quz' => 'qux',
        ]) extends Model {
            use EncryptsJson;
            protected $fillable = ['foo', 'quz'];
        };

        $this->expectException(JsonException::class);

        $encrypted = Crypt::encrypt('invalid', false);

        $class::fromEncryptedJson($encrypted, JSON_THROW_ON_ERROR);
    }

    public function test_exception_when_invalid_encrypted_string()
    {
        $class = new class([
            'foo' => 'bar',
            'quz' => 'qux',
        ]) extends Model {
            use EncryptsJson;
            protected $fillable = ['foo', 'quz'];
        };

        $this->expectException(DecryptException::class);

        $encrypted = 'invalid';

        $class::fromEncryptedJson($encrypted);
    }
}
