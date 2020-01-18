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
     * @param  null|array  $rules
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data = null, array $rules = null)
    {
        return app(Factory::class)->make(
            $data ?? $this->validationData(),
            $rules ?? $this->validationRules(),
            $this->validationMessages(),
            $this->customAttributes()
        );
    }

    /**
     * Run the validator's rules against its data.
     *
     * @param  null|array  $data
     * @param  null|array  $rules
     * @param  null|callable|string  $after
     * @return bool
     */
    public function validates(array $data = null, array $rules = null, $after = null)
    {
        $validator = $this->validator($data, $rules);

        if ($after) {
            $validator->after($after);
        }

        $result = $validator->fails();

        $this->validated = $validator->validated();

        return $result;
    }

    /**
     * Validates the class data through a Validator
     *
     * @param  null|array  $data
     * @param  null|array  $rules
     * @param  null|callable|string  $after
     * @return array
     *
     */
    public function validate(array $data = null, array $rules = null, $after = null)
    {
        $validator = $this->validator($data, $rules);

        if ($after) {
            $validator->after($after);
        }

        return $this->validated = $validator->validate();
    }

    /**
     * Returns the validated data.
     *
     * @return array
     */
    public function validated()
    {
        return $this->validated;
    }

    /**
     * Returns the data array to use against the Validator
     *
     * @return array
     */
    protected function validationData()
    {
        throw new LogicException('Can\'t validate ' . class_basename($this) . ' without data.');
    }

    /**
     * The array of rules
     *
     * @return array
     */
    protected function validationRules()
    {
        throw new LogicException('The class ' . class_basename($this) . ' has no validation rules.');
    }

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
