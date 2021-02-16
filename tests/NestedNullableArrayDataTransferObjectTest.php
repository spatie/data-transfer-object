<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class NestedNullableArrayDataTransferObjectTest extends TestCase
{
    /** @test */
    public function it_can_validate_nested_arrays()
    {
        $a = new WrapperWithNullableArrayDtoPropertyType([
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
        $a = new WrapperWithNullableArrayDtoPropertyType([
            'arr' => [],
        ]);

        $this->assertIsArray($a->arr);
        $this->assertEmpty($a->arr);
    }

    /** @test */
    public function it_fails_on_null()
    {
        $a = new WrapperWithNullableArrayDtoPropertyType([
            'arr' => null,
        ]);

        $this->assertNull($a->arr);
    }
}

class WrapperWithNullableArrayDtoPropertyType extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\NestedArrayDtoPropertyType[] */
    public ?array $arr;
}
