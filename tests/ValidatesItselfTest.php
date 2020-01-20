<?php

namespace DarkGhostHunter\Laratraits\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use DarkGhostHunter\Laratraits\ValidatesItself;

class ValidatesItselfTest extends TestCase
{
    public function testValidatesFromExternalData()
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

    public function testValidatesFromInternalData()
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

    public function testValidatesExternalDataOverInternalData()
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

    public function testValidatesReturnTrueWhenValid()
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

    public function testValidatedReturnsValidatedValues()
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

    public function testPassesValidationMessages()
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

    public function testPassesCustomAttributes()
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

    public function testPassValidationAfterCallback()
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

    public function testExceptionWhenNoDataToValidate()
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
