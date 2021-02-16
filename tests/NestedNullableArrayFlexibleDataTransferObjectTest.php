<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\FlexibleDataTransferObject;

class NestedNullableArrayFlexibleDataTransferObjectTest extends TestCase
{
    /** @test */
    public function it_can_validate_nested_arrays()
    {
        $a = new FlexibleWrapperWithNullableArrayDtoPropertyType([
            'arr' => [
                ['name' => 'test',],
                ['name' => 'test2',]
            ],
        ]);

        $this->assertEquals('test', $a->arr[0]->name);
        $this->assertEquals('test2', $a->arr[1]->name);
    }

    /** @test */
    public function it_can_handle_empty_arrays()
    {
        $a = new FlexibleWrapperWithNullableArrayDtoPropertyType([
            'arr' => [],
        ]);

        $this->assertIsArray($a->arr);
        $this->assertEmpty($a->arr);
    }

    /** @test */
    public function it_fails_on_null()
    {
        $a = new FlexibleWrapperWithNullableArrayDtoPropertyType([
            'arr' => null,
        ]);

        $this->assertNull($a->arr);
    }

    /** @test */
    public function it_fails_on_missing()
    {
        $a = new FlexibleWrapperWithNullableArrayDtoPropertyType([]);

        $this->assertNull($a->arr);
    }
}

class FlexibleWrapperWithNullableArrayDtoPropertyType extends FlexibleDataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\NestedArrayDtoPropertyType[] */
    public ?array $arr;
}
