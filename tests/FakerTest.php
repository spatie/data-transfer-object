<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Tests\Dummy\BasicDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithCast;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithCastedAttributeHavingCast;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithNullableProperty;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObject;
use Spatie\DataTransferObject\Tests\Dummy\UnionDto;
use Spatie\DataTransferObject\Tests\Dummy\WithDefaultValueDto;

class FakerTest extends TestCase
{
    public function test_default_value_works()
    {
        $dto1 = WithDefaultValueDto::fake();
        $dto2 = new WithDefaultValueDto();

        $this->assertEquals($dto1->name, $dto2->name);
    }

    public function test_nullable_works()
    {
        $dto = ComplexDtoWithNullableProperty::fake();

        $this->assertTrue($dto->other instanceof BasicDto);
    }

    public function test_builtin_types_works()
    {
        $dto = BasicDto::fake();

        $this->assertTrue(is_string($dto->name));
    }

    public function test_union_dto_works()
    {
        $dto = UnionDto::fake();

        $this->assertNotNull($dto->foo);
    }

    public function test_complex_dto_works()
    {
        $dto = ComplexDto::fake();

        $this->assertTrue(is_string($dto->name));
        $this->assertTrue(is_string($dto->other->name));
    }

    public function test_passing_value_works()
    {
        $name = 'NAME';
        $dto = BasicDto::fake(
            name: $name
        );

        $this->assertEquals($name, $dto->name);
    }

    public function test_passing_nested_value_works()
    {
        $name = 'NAME';
        $dto = ComplexDto::fake(
            other: ['name' => $name]
        );

        $this->assertEquals($name, $dto->other->name);
    }

    public function test_caster_works()
    {
        $dto1 = ComplexDtoWithCast::fake();
        $dto2 = ComplexDtoWithCastedAttributeHavingCast::fake();

        $this->assertTrue($dto1->object instanceof ComplexObject);
        $this->assertTrue($dto2->other->object instanceof ComplexObject);
    }
}
