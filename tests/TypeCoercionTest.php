<?php
namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithAllTypes;

class TypeCoercionTest extends TestCase
{
    /** @test */
    public function create_with_exact_types()
    {
        $dto = new ComplexDtoWithAllTypes(
            string: "test",
            int: 5,
            bool: false,
            float: 5.0,
            basicDto: [
                'name' => 'test'
            ]
        );

        $this->assertEquals('test', $dto->string);
    }
    /** @test */
    public function create_with_coercible_string()
    {
        try {
            $dto = new ComplexDtoWithAllTypes(
                string: 1,
                int: 5,
                bool: false,
                float: 5.0,
                basicDto: [
                    'name' => 'test'
                ]
            );
        } catch (\TypeError $typeError){
            $this->assertStringContainsString("must be of type string", $typeError->getMessage());
            $this->markTestSucceeded();
        }
    }
    /** @test */
    public function create_with_coercible_int()
    {
        try {
            $dto = new ComplexDtoWithAllTypes(
                string: "test",
                int: "5",
                bool: false,
                float: 5.0,
                basicDto: [
                    'name' => 'test'
                ]
            );
        } catch (\TypeError $typeError){
            $this->assertStringContainsString("must be of type int", $typeError->getMessage());
            $this->markTestSucceeded();
        }
    }
    /** @test */
    public function create_with_coercible_bool()
    {
        try {
            $dto = new ComplexDtoWithAllTypes(
                string: "test",
                int: 5,
                bool: "false",
                float: 5.0,
                basicDto: [
                    'name' => 'test'
                ]
            );
        } catch (\TypeError $typeError){
            $this->assertStringContainsString("must be of type bool", $typeError->getMessage());
            $this->markTestSucceeded();
        }
    }
    /** @test */
    public function create_with_coercible_float()
    {
        // floats coercion is allowed
        $dto = new ComplexDtoWithAllTypes(
            string: "test",
            int: 5,
            bool: false,
            float: 5,
            basicDto: [
                'name' => 'test'
            ]
        );
        $this->markTestSucceeded();
    }
}
