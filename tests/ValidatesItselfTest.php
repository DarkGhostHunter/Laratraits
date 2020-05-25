<?php

namespace DarkGhostHunter\Laratraits\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use DarkGhostHunter\Laratraits\ValidatesItself;

class ValidatesItselfTest extends TestCase
{
    public function test_validates_from_external_data()
    {
        $validatable = new class() {
            use ValidatesItself;

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }
        };

        $validated = $validatable->validate([
            'foo' => 'bar'
        ]);

        $this->assertEquals(['foo' => 'bar'], $validated);
    }

    public function test_validates_from_internal_data()
    {
        $validatable = new class() {
            use ValidatesItself;

            protected function validationData()
            {
                return [
                    'foo' => 'bar'
                ];
            }

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }
        };

        $validated = $validatable->validate();

        $this->assertEquals(['foo' => 'bar'], $validated);
    }

    public function test_validates_external_data_over_internal_data()
    {
        $validatable = new class() {
            use ValidatesItself;

            protected function validationData()
            {
                return [
                    'qux' => 'quz'
                ];
            }

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }
        };

        $validated = $validatable->validate([
            'foo' => 'bar'
        ]);

        $this->assertEquals(['foo' => 'bar'], $validated);
    }

    public function test_validates_return_true_when_valid()
    {
        $validatable = new class() {
            use ValidatesItself;

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }
        };

        $this->assertTrue($validatable->validates(['foo' => 'bar']));
        $this->assertFalse($validatable->validates(['foo' => 'not_bar']));
    }

    public function test_validated_returns_validated_values()
    {
        $validatable = new class() {
            use ValidatesItself;

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }
        };

        $this->assertTrue($validatable->validates(['foo' => 'bar']));
        $this->assertEquals(['foo' => 'bar'], $validatable->validated());
        $this->assertEquals(['foo' => 'bar'], $validatable->validate(['foo' => 'bar']));
    }

    public function test_passes_validation_messages()
    {
        $validatable = new class() {
            use ValidatesItself;

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }

            protected function validationMessages()
            {
                return [
                    'foo.in' => 'Foo validation message.'
                ];
            }
        };

        try {
            $validatable->validate(['foo' => 'not_foo']);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'foo' => [
                    'Foo validation message.'
                ]
            ], $exception->errors());
        }
    }

    public function test_passes_custom_attributes()
    {
        $validatable = new class() {
            use ValidatesItself;

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }

            protected function customAttributes()
            {
                return [
                    'foo' => 'Foo Custom Attribute'
                ];
            }
        };

        try {
            $validatable->validate(['foo' => 'not_foo']);
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'foo' => [
                    'The selected Foo Custom Attribute is invalid.'
                ]
            ], $exception->errors());
        }
    }

    public function test_pass_validation_after_callback()
    {
        $validatable = new class() {
            use ValidatesItself;

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }
        };

        try {
            $validatable->validate(['foo' => 'not_bar'], function (Validator $validator) {
                $validator->errors()->add('quz', 'qux');
            });
        } catch (ValidationException $exception) {
            $this->assertEquals([
                'foo' => ['The selected foo is invalid.'],
                'quz' => ['qux'],
            ], $exception->errors());
        }


    }

    public function test_exception_when_no_data_to_validate()
    {
        $this->expectException(\LogicException::class);

        $validatable = new class () {
            use ValidatesItself;

            protected function validationRules() : array
            {
                return [
                    'foo' => 'required|in:bar'
                ];
            }
        };

        $validatable->validate();
    }
}
