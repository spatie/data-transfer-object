<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class NestedArrayDataTransferObjectTest extends TestCase
{
    /** @test */
    public function it_can_validate_nested_arrays()
    {
        $a = new WrapperDtoPropertyType([
            'arr' => [
                ['name' => 'test',],
                ['name' => 'test2',]
            ],
        ]);

        $this->assertEquals('test', $a->arr[0]->name);
        $this->assertEquals('test2', $a->arr[1]->name);
        $this->assertEquals(
            NestedArrayDtoPropertyType::class,
            get_class($a->arr[0])
        );
    }

    /** @test */
    public function it_can_handle_empty_arrays()
    {
        $a = new WrapperDtoPropertyType([
            'arr' => [],
        ]);

        $this->assertIsArray($a->arr);
        $this->assertEmpty($a->arr);
    }
}

class WrapperDtoPropertyType extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\NestedArrayDtoPropertyType[] */
    public array $arr;
}

class NestedArrayDtoPropertyType extends DataTransferObject
{
    public string $name;
}
