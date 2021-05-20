<?php
/**
 * Enumerable
 *
 * This class it's a simpler rework of the "Enumerate" class. It works by receiving a list of
 * states and letting the developer to set one of these state. It also includes helpers for
 * getting all states, current state and programmatically setting a state by a condition.
 *
 * You can return a state list by an array, a \Traversable, or a list separated by comma:
 *
 *     $list = Enumerable::from('foo,bar,quz', 'foo');
 *
 * Anyway, it's encouraged to use your own class and extend this for better control of states.
 *
 *     class WeatherEnumerable extends Enumerable
 *     {
 *         protected $current = 'sunny';
 *
 *         protected $states = ['sunny', 'cloudy', 'rainy', 'windy', 'stormy', 'snowy'];
 *
 *         // ...
 *      }
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

namespace DarkGhostHunter\Laratraits;

use BadMethodCallException;
use Countable;
use LogicException;
use Traversable;

class Enumerable implements Countable
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
     * @param  string|array|iterable  $states
     */
    public function __construct($states = null)
    {
        foreach ($this->statesToArray($states) as $state) {
            $this->states[] = $state;
        }

        if (empty($this->states())) {
            throw new LogicException('The ' . static::class . ' does not have states to set.');
        }

        if ($this->current) {
            $this->assign($this->current);
        }
    }

    /**
     * Normalize the states to an array of states.
     *
     * @param  string|array|iterable  $states
     * @return array
     */
    protected function statesToArray($states)
    {
        if ($states instanceof Traversable) {
            $states = iterator_to_array($states);
        }
        elseif (is_string($states)) {
            return explode(',', $states);
        }

        return (array)$states;
    }

    /**
     * Return the current state, or null when uninitialized.
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
     * @return array
     */
    public function states()
    {
        return $this->states;
    }

    /**
     * Assigns a state.
     *
     * @param  string  $name
     * @return $this
     */
    public function assign(string $name)
    {
        // We won't normalize the name here since we can only set one state at a time. We will
        // hard-check if the state exists in the array, and if it is, we will properly set it.
        // Otherwise, we'll throw an exception telling the developer this value is incorrect.
        if (in_array($name, $this->states())) {
            $this->current = $name;

            return $this;
        }

        throw new LogicException("The state '{$name}' doesn't exists in this Enumerate instance.");
    }

    /**
     * Sets a state when a given condition evaluates to true.
     *
     * @param  bool|\Closure  $condition
     * @param  string|null  $state
     * @return $this
     */
    public function when($condition, $state)
    {
        if (value($condition)) {
            return $this->assign($state);
        }

        return $this;
    }

    /**
     * Sets a state when a given condition evaluates to false.
     *
     * @param  bool|\Closure  $condition
     * @param  string  $state
     * @return $this
     */
    public function unless($condition, string $state)
    {
        return $this->when(! value($condition), $state);
    }

    /**
     * Returns if the current state is equal to at least one of the issued states.
     *
     * @param  string|array|iterable  $state
     * @return bool
     */
    public function is($state)
    {
        foreach ($this->statesToArray($state) as $value) {
            if ($this->current === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns if the current state is not equal to the issued state.
     *
     * @param  string|array|iterable  $state
     * @return bool
     */
    public function isNot($state)
    {
        return ! $this->is($state);
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->states());
    }

    /**
     * Transform the object into a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->current();
    }

    /**
     * Handle dynamically setting the state.
     *
     * @param  string  $name
     * @param  array  $arguments
     * @return \DarkGhostHunter\Laratraits\Enumerable
     */
    public function __call($name, $arguments)
    {
        try {
            return $this->assign($name);
        }
        catch (LogicException $exception) {
            throw new BadMethodCallException('Call to undefined method ' . static::class . '::' . $name . '()');
        }
    }

    /**
     * Creates a new Enumerate instance using a list of states.
     *
     * @param  array|iterable  $states
     * @param  string|null  $initial
     * @return mixed
     */
    public static function from($states, string $initial = null) : self
    {
        $instance = (new static($states));

        if ($initial) {
            $instance->assign($initial);
        }

        return $instance;
    }

    /**
     * Creates a new Enumerate instance, using an initial state if not null.
     *
     * @param  string|null  $initial
     * @return mixed
     */
    public static function as(string $initial = null) : self
    {
        return (new static)->when($initial, $initial);
    }
}
