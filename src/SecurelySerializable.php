<?php
/**
 * SecurelySerializable
 *
 * This trait allows any serializable object to have a "signature" that is created on serialization,
 * and later checked before unserialization. Since this trait is optional to the process, you must
 * use the `addSignature` when serializing the object, and `checkSignature` to check the data.
 *
 *     class Foo
 *     {
 *         use SecurelySerializable;
 *
 *         protected $foo;
 *
 *         public function __serialize() : array
 *         {
 *             return $this->addSignature(['foo' => 'bar']);
 *         }
 *
 *         public function __unserialize(array $data) : void
 *         {
 *             $this->checkSignature($data);
 *
 *             $this->foo = $data['foo'];
 *         }
 *     }
 *
 * If the unserialized data has been tampered with, an `InvalidArgumentException` is thrown.
 *
 * As a side note, the `checkSignature` always returns the data without the signature.
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

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Hashing\Hasher;

trait SecurelySerializable
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
        ksort($data);

        return $data + [
                $this->signatureKey() => $this->signatureChecker()->make(json_encode($data, $options)),
            ];
    }

    /**
     * Checks if the the unserialized array has not been tampered with.
     *
     * @param  array  $data
     * @param  null|mixed  $options
     * @return array
     */
    protected function checkSignature(array $data, $options = null) : array
    {
        $key = $this->signatureKey();

        if (isset($data[$key]) && is_string($data['signature'])) {
            $signature = Arr::pull($data, $key);

            ksort($data);

            if ($this->signatureChecker()->check(json_encode($data, $options), $signature)) {
                return $data;
            }
        }

        throw new InvalidArgumentException('The object ' . static::class . ' has an invalid signature.');
    }

    /**
     * Return the serialization signature key to check.
     *
     * @return string
     */
    protected function signatureKey()
    {
        return defined('static::SIGNATURE_KEY') ? static::SIGNATURE_KEY : 'signature';
    }

    /**
     * The hasher instance to check the signature against the data.
     *
     * @return \Illuminate\Contracts\Hashing\Hasher
     */
    protected function signatureChecker() : Hasher
    {
        return Hash::driver(defined('static::SIGNATURE_CHECKER') ? static::SIGNATURE_CHECKER : null);
    }
}