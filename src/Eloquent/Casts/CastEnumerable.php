<?php
/**
 * CastEnumerable
 *
 * This class allows a Model to cast an "enum" property into a Enumerable instance with its own
 * list of available states. Because this class needs the possible states declared beforehand
 * this is made an abstract class you will need to extend with the states you want to have.
 *
 *     class WeatherEnumerable extends CastEnumerable
 *     {
 *         protected $current = 'sunny';
 *
 *         protected $states = ['sunny', 'cloudy', 'rainy', 'windy', 'stormy', 'snowy'];
 *
 *         // ...
 *      }
 *
 * Then, you can create a custom casting your Model attributes:
 *
 *     protected $casts = [
 *         'weather' => WeatherEnumerable::class,
 *     ];
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
 * Laravel is a Trademark of Taylor Otwell. Copyright © 2011-2021 Laravel LLC.
 *
 * @link https://github.com/DarkGhostHunter/Laratraits
 */

namespace DarkGhostHunter\Laratraits\Eloquent\Casts;

use DarkGhostHunter\Laratraits\Enumerable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

/**
 * @deprecated Use PHP native enums instead
 * @link https://wiki.php.net/rfc/enumerations
 */
abstract class CastEnumerable extends Enumerable implements CastsAttributes
{
    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $this->when($value, $value);
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        $this->when($value, $value);

        return (string)$this->current();
    }
}
