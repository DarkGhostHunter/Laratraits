<?php
/**
 * Enumerable States
 *
 * This traits allows a given class to have one of an strictly enumerated list of states.
 *
 * You can override the Enumerable instance with your own custom class:
 *
 *     protected makeEnumerableInstance()
 *     {
 *         return new WeatherEnumerable;
 *     }
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

use LogicException;

trait EnumerableStates
{
    /**
     * The Enumerable instance.
     *
     * @var \DarkGhostHunter\Laratraits\Enumerable
     */
    protected $enumerable;

    /**
     * Set a state for this current instance.
     *
     * @param  string  $state
     * @return $this
     */
    public function assign(string $state)
    {
        $this->getEnumerable()->assign($state);

        return $this;
    }

    /**
     * Returns the current state, or null if uninitialized.
     *
     * @return string|null
     */
    public function current()
    {
        return $this->getEnumerable()->current();
    }

    /**
     * Returns the current Enumerable instance.
     *
     * @return \DarkGhostHunter\Laratraits\Enumerable
     */
    public function getEnumerable()
    {
        return $this->enumerable = $this->enumerable ?? $this->makeEnumerableInstance();
    }

    /**
     * Creates a new Enumerable instance.
     *
     * @return \DarkGhostHunter\Laratraits\Enumerable
     */
    protected function makeEnumerableInstance()
    {
        return Enumerable::from($this->getEnumerableStates(), $this->getEnumerableInitialState());
    }

    /**
     * Return the states for the current instance.
     *
     * @return array|iterable
     */
    protected function getEnumerableStates() : iterable
    {
        if (defined('static::STATES')) {
            return static::STATES;
        }

        throw new LogicException('The current ' . static::class . ' instance has no states defined.');
    }

    /**
     * Return the initial state, if any.
     *
     * @return string|void
     */
    protected function getEnumerableInitialState()
    {
        if (defined('static::STATE_INITIAL')) {
            return static::STATE_INITIAL;
        }
    }
}
