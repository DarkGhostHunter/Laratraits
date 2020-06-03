<?php
/**
 * EncryptsJson
 *
 * This trait allows to serialize the model as an encrypted JSON string automatically.
 *
 *     $model = new Model(['foo' => 'bar']);
 *
 *     echo $model->toJson(); // "eyJpdiI6IlZBRVJ5WlI0bUJPNzkxWHRVcj..."
 *
 * Encrypting the model JSON representation can be useful to transmit it or store it without
 * giving the model properties away. You can also restore a model instance using the static
 * method `fromEncryptedJson()` with the previously encrypted JSON string, and prepare it.
 *
 *     protected function afterJsonDecryption()
 *     {
 *         $this->received_at = now();
 *     }
 *
 *     $model = Model::fromEncryptedJson($json);
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

namespace DarkGhostHunter\Laratraits\Eloquent;

trait EncryptsJson
{
    /**
     * Convert the model instance to encrypted JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \Illuminate\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0)
    {
        return encrypt(parent::toJson($options), false);
    }

    /**
     * Prepares the model after being instanced from decrypted JSON.
     *
     * @return void
     */
    public function afterJsonDecryption()
    {
        //
    }

    /**
     * Creates a new model instance from an encrypted JSON string.
     *
     * @param  string  $encrypted
     * @param  int  $options
     * @return static
     */
    public static function fromEncryptedJson(string $encrypted, $options = 0)
    {
        $array = json_decode(decrypt($encrypted, false), true, 512, $options);

        $instance = new static($array ?? []);

        $instance->afterJsonDecryption();

        return $instance;
    }
}
