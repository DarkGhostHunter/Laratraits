<?php
/**
 * Enumerable States
 *
 * This traits allows a given class to have one of an strictly enumerated list of states.
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

use LogicException;

trait EnumerableStates
{
    /**
     * The Enumerate instance.
     *
     * @var \DarkGhostHunter\Laratraits\Enumerate
     */
    protected $enumerate;

    /**
     * Set the state for this current instance.
     *
     * @param $state
     * @return $this
     */
    public function state(string $state = null)
    {
        $this->getEnumerate()->{$state}();

        return $this;
    }

    /**
     * Returns the current state.
     *
     * @return string
     */
    public function current()
    {
        return $this->getEnumerate()->current();
    }

    /**
     * Create an Enumerate instance.
     *
     * @return \DarkGhostHunter\Laratraits\Enumerate|mixed
     */
    protected function getEnumerate()
    {
        if ($this->enumerate) {
            return $this->enumerate;
        }

        if (defined('static::STATES')) {
            $states = static::STATES;
        }
        elseif (method_exists($this, 'states')) {
            $states = $this->states();
        }
        else {
            $class = static::class;
            throw new LogicException("The current {$class} has no states defined.");
        }

        if (defined('static::STATE_INITIAL')) {
            $initial = static::STATE_INITIAL;
        }
        elseif (method_exists($this, 'initial')) {
            $initial = $this->initial();
        }

        return $this->enumerate = Enumerate::from($states, $initial ?? null);
    }
}
