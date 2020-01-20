<?php

namespace DarkGhostHunter\Laratraits;

use LogicException;
use Illuminate\Contracts\Validation\Factory;

/**
 * Trait ValidatesItself
 * ---
 * This trait allows a class to validate itself using something like "$class->validate()". You can
 * issue your own data to be validated, but preferably you should let the class point the data
 * itself. You can use a Closure to execute an "after callback" in the Validator instance.
 *
 * @package DarkGhostHunter\Laratraits
 */
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
        throw new LogicException('The class ' . class_basename($this) . ' has no data to validate.');
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
