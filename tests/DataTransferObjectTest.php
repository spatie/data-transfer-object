<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Tests\Dummy\BasicDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithCastedAttributeHavingCast;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithNullableProperty;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithParent;
use Spatie\DataTransferObject\Tests\Dummy\ComplexDtoWithSelf;
use Spatie\DataTransferObject\Tests\Dummy\ComplexStrictDto;
use Spatie\DataTransferObject\Tests\Dummy\WithDefaultValueDto;

class DataTransferObjectTest extends TestCase
{
    /** @test */
    public function array_of()
    {
        $list = BasicDto::arrayOf([
            ['name' => 'a'],
            ['name' => 'b'],
        ]);

        $this->assertCount(2, $list);

        $this->assertEquals('a', $list[0]->name);
        $this->assertEquals('b', $list[1]->name);
    }

    /** @test */
    public function create_with_nested_dto()
    {
        $dto = ComplexDto::new([
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
    }

    /** @test */
    public function create_with_nested_dto_already_casted()
    {
        $dto = ComplexDto::new([
            'name' => 'a',
            'other' => BasicDto::new([
                'name' => 'b',
            ]),
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
    }

    /** @test */
    public function create_strict_with_nested_dto()
    {
        $dto = ComplexStrictDto::new([
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
    }

    /** @test */
    public function create_strict_with_nested_dto_already_casted()
    {
        $dto = ComplexStrictDto::new([
            'name' => 'a',
            'other' => BasicDto::new([
                'name' => 'b',
            ]),
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
    }

    /** @test */
    public function create_with_null_nullable_nested_dto()
    {
        $dto = ComplexDtoWithNullableProperty::new([
            'name' => 'a',
            'other' => null,
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertNull($dto->other);
    }

    /** @test */
    public function create_with_nested_dto_having_cast()
    {
        $dto = ComplexDtoWithCastedAttributeHavingCast::new([
            'name' => 'a',
            'other' => [
                'name' => 'b',
                'object' => [
                    'name' => 'c',
                ],
            ],
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
        $this->assertEquals('c', $dto->other->object->name);
    }

    /** @test */
    public function all_with_nested_dto()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = ComplexDto::new($array);

        $all = $dto->all();

        $this->assertCount(2, $all);
        $this->assertEquals('a', $all['name']);
        $this->assertEquals('b', $all['other']->name);
    }

    /** @test */
    public function to_array_with_nested_dto()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = ComplexDto::new($array);

        $this->assertEquals($array, $dto->toArray());
    }

    /** @test */
    public function to_array_with_only()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = ComplexDto::new($array);

        $this->assertEquals(['name' => 'a'], $dto->only('name')->toArray());
    }

    /** @test */
    public function to_array_with_except()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = ComplexDto::new($array);

        $this->assertEquals(['other' => ['name' => 'b']], $dto->except('name')->toArray());
    }

    /** @test */
    public function create_with_default_value()
    {
        $dto = WithDefaultValueDto::new();

        $this->assertEquals(['name' => 'John'], $dto->toArray());
    }

    /** @test */
    public function create_with_overriden_default_value()
    {
        $dto = WithDefaultValueDto::new(name: 'Doe');

        $this->assertEquals(['name' => 'Doe'], $dto->toArray());
    }

    /** @test */
    public function test_clone()
    {
        $array = [
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ];

        $dto = ComplexDto::new($array);

        $clone = $dto->clone(other: ['name' => 'a']);

        $this->assertEquals('a', $clone->name);
        $this->assertEquals('a', $clone->other->name);
    }

    /** @test */
    public function create_with_nested_self()
    {
        $dto = ComplexDtoWithSelf::new([
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
        $this->assertNull($dto->other->other);
    }

    /** @test */
    public function create_with_nested_parent()
    {
        $dto = ComplexDtoWithParent::new([
            'name' => 'a',
            'other' => [
                'name' => 'b',
            ],
        ]);

        $this->assertEquals('a', $dto->name);
        $this->assertEquals('b', $dto->other->name);
        $this->assertNull($dto->other->other);
    }
}
