<?php
/**
 * Enumerate
 *
 * This class aids you in enumerating values from a given array (or ArrayAccess), and allows
 * setting the state using the name of the state as a method. Additionally, it comes with
 * handy methods to check the current state, state value, and all the possible states.
 *
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

use Countable;
use Traversable;
use LogicException;
use JsonSerializable;
use BadMethodCallException;
use Illuminate\Contracts\Support\Jsonable;

/**
 * @deprecated Use \DarkGhostHunter\Laratraits\Enumerable instead
 */
class Enumerate implements Countable, JsonSerializable, Jsonable
{
    /**
     * Current state of the enumerated object.
     *
     * @var string
     */
    protected $current;

    /**
     * Enumerate the possible states for this current instance.
     *
     * @var array
     */
    protected $states = [];

    /**
     * Create a new Enumerated instance with a list of available states.
     *
     * @param  array|iterable  $states
     */
    public function __construct($states = null)
    {
        if ($states) {

            $states = $states instanceof Traversable
                ? iterator_to_array($states)
                : (array)$states;

            foreach ($states as $key => $state) {
                $this->states[$key] = $state;
            }
        }

        if ($this->current) {
            $this->set($this->current);
        }
    }

    /**
     * Return the enumerated value.
     *
     * @return mixed
     */
    public function value()
    {
        return array_key_exists($this->current, $this->states)
            ? $this->states[$this->current]
            : $this->current;
    }

    /**
     * Returns if the state exists.
     *
     * @param  string|array  $state
     * @return bool
     */
    public function has($state)
    {
        foreach ((array)$state as $value) {
            if (array_key_exists($value, $this->states) || in_array($value, $this->states, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if the current state is equal to the issued state.
     *
     * @param  string|array  $state
     * @return bool
     */
    public function is($state)
    {
        foreach ((array)$state as $value) {
            if ($this->current === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if the current state is not equal to the issued state.
     *
     * @param $state
     * @return bool
     */
    public function isNot($state)
    {
        return ! $this->is($state);
    }


    /**
     * Return the current state.
     *
     * @return string|null
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Return the possible states of this instance.
     *
     * @return array|\ArrayAccess
     */
    public function states()
    {
        return $this->states;
    }

    /**
     * Sets a state.
     *
     * @param $name
     * @return $this
     */
    public function set($name)
    {
        if ($this->has($name)) {
            $this->current = $name;

            return $this;
        }

        throw new LogicException("The state [$name] doesn't exists in this Enumerate instance.");
    }


    /**
     * Handle dynamically setting the state.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return \DarkGhostHunter\Laratraits\Enumerate
     *
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        try {
            return $this->set($name);
        } catch (LogicException $exception) {
            throw new BadMethodCallException('Call to undefined method ' . static::class . '::' . $name);
        }
    }

    /**
     * Transform the class instance into a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->current ?? '';
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->states);
    }

    /**
     * @inheritDoc
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize());
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->value();
    }

    /**
     * Creates a new Enumerate instance.
     *
     * @param  array|iterable  $states
     * @param  string|null  $initial
     * @return mixed
     */
    public static function from($states, string $initial = null) : self
    {
        $instance = (new static($states));

        if ($initial) {
            $instance->set($initial);
        }

        return $instance;
    }
}
