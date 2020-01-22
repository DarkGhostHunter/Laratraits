<?php
/**
 * Validates Itself
 *
 * This trait allows a class to validate itself using something like "$class->validate()". You can
 * issue your own data to be validated, but preferably you should let the class point the data
 * itself. You can use a Closure to execute an "after callback" in the Validator instance.
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
 *
 */

namespace DarkGhostHunter\Laratraits;

use LogicException;
use Illuminate\Contracts\Validation\Factory;

trait ValidatesItself
{
    /**
     * The validated data.
     *
     * @var array
     */
    protected $validated = [];

    /**
     * Creates a validator instance.
     *
     * @param  null|array  $data
     * @param  null|callable|string  $after
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data = null, $after = null)
    {
        $validator = app(Factory::class)->make(
            $data ?? $this->validationData(),
            $this->validationRules(),
            $this->validationMessages(),
            $this->customAttributes()
        );

        if ($after) {
            $validator->after($after);
        }

        return $validator;
    }

    /**
     * Run the validator's rules against its data and returns if it passes or not.
     *
     * @param  null|array  $data
     * @param  null|callable|string  $after
     * @return bool
     */
    public function validates(array $data = null, $after = null)
    {
        $validator = $this->validator($data, $after);

        if ($validator->fails()) {
            return false;
        }

        $this->validated = $validator->validated();

        return true;
    }

    /**
     * Validates the class and returns the validated data, or throws an exception.
     *
     * @param  null|array  $data
     * @param  null|callable|string  $after
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(array $data = null, $after = null)
    {
        return $this->validated = $this->validator($data, $after)->validate();
    }

    /**
     * Returns the validated data. Will return null if no validated data is available.
     *
     * @return null|array
     */
    public function validated()
    {
        return $this->validated;
    }

    /**
     * Returns the data array to use against the Validator.
     *
     * @return array
     */
    public function validationData()
    {
        throw new LogicException('The class ' . class_basename($this) . ' has no default data to validate.');
    }

    /**
     * The array of rules.
     *
     * @return array
     */
    abstract protected function validationRules() : array;

    /**
     * The array of custom error messages.
     *
     * @return array
     */
    protected function validationMessages()
    {
        return [];
    }

    /**
     * The array of custom attribute names.
     *
     * @return array
     */
    protected function customAttributes()
    {
        return [];
    }
}
