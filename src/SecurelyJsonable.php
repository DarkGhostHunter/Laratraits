<?php
/**
 * SecurelyJsonable
 *
 * This trait allows any Jsonable object to contain a signature that avoids tampering the
 * contents. You can use any key, like a secret key contained in your database, or the
 * application key. On unserialization, check if the key is valid before proceeding.
 *
 * For example, you can add the signature for the JSON serialization:
 *
 *     class Foo
 *     {
 *         use SecurelyJsonable;
 *
 *         protected $foo;
 *
 *         public function toJson() : array
 *         {
 *             return $this->addSignature(['foo' => 'bar']);
 *         }
 *
 *         public static function fromJson(array $data) : void
 *         {
 *             $this->checkSignature($data);
 *
 *             $this->foo = $data['foo'];
 *         }
 *
 *         protected static function hashKey()
 *         {
 *             return config('app.key');
 *         }
 *     }
 *
 * **WARNING**. If you're using PHP 7.2 or below, avoid using native serialization.
 *
 * ---
 * MIT License
 *
 * Copyright (c) Italo Israel Baeza Cabrera
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2020 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits;

use LogicException;
use InvalidArgumentException;

trait SecurelyJsonable
{
    /**
     * Securely serialize the object.
     *
     * @param  array  $data
     * @param  null|mixed  $options
     * @return array
     */
    protected function addSignature(array $data, $options = null) : array
    {
        $key = static::signatureKey();

        // If the signature key was set, we will fail because the hash will overwrite it.
        if (isset($data[$key])) {
            throw new LogicException("The key [{$key}] is reserved to store the object signature.");
        }

        $original = $data;

        ksort($data);

        return $original + [$key => static::makeSignature($data, $options)];
    }

    /**
     * Makes the signature for the serialized class.
     *
     * @param  array  $data
     * @param  null  $options
     * @return string
     */
    protected static function makeSignature(array $data, $options = null)
    {
        return hash_hmac('sha256', json_encode($data, $options), static::hashKey());
    }

    /**
     * Checks if the the unserialized array has not been tampered with.
     *
     * @param  array  $data
     * @param  null|mixed  $options
     * @return array  The original array with the signature attached.
     */
    protected static function checkSignature(array $data, $options = null) : array
    {
        $key = static::signatureKey();

        if (isset($data[$key]) && is_string($data[$key])) {
            $signature = $data[$key];

            unset($data[$key]);

            $original = $data;

            ksort($data);

            if (hash_equals(static::makeSignature($data, $options), $signature)) {
                return $original;
            }
        }

        throw new InvalidArgumentException('The object ' . static::class . ' has an invalid signature.');
    }

    /**
     * Return the serialization signature key to check.
     *
     * @return string
     */
    protected static function signatureKey()
    {
        return defined('static::SIGNATURE_KEY') ? static::SIGNATURE_KEY : 'signature';
    }

    /**
     * Returns the key to use for the signature hash.
     *
     * @return string
     */
    abstract protected static function hashKey() : string;
}