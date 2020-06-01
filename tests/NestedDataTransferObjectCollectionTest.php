<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectCollection;
use Spatie\DataTransferObject\DataTransferObjectError;

class NestedDataTransferObjectCollectionTest extends TestCase
{
    /** @test */
    public function with_typed_properties()
    {
        $a = new DtoParentPropertyType([
            'children' => [
                ['name' => 'test'],
                ['name' => 'test2'],
                ['name' => 'test3'],
            ],
        ]);

        $this->assertEquals('test', $a->children[0]->name);
        $this->assertEquals('test2', $a->children[1]->name);
        $this->assertEquals('test3', $a->children[2]->name);
    }

    /** @test */
    public function with_parent_docblock_property()
    {
        $a = new DtoParentPropertyTypeUsingDocblock([
            'children' => [
                ['name' => 'test'],
                ['name' => 'test2'],
                ['name' => 'test3'],
            ],
        ]);

        $this->assertEquals('test', $a->children[0]->name);
        $this->assertEquals('test2', $a->children[1]->name);
        $this->assertEquals('test3', $a->children[2]->name);
    }

    /** @test */
    public function with_collection_docblock()
    {
        $a = new DtoParentPropertyTypeWithCollectionDocblock([
            'children' => [
                ['name' => 'test'],
                ['name' => 'test2'],
                ['name' => 'test3'],
            ],
        ]);

        $this->assertEquals('test', $a->children[0]->name);
        $this->assertEquals('test2', $a->children[1]->name);
        $this->assertEquals('test3', $a->children[2]->name);
    }

    /** @test */
    public function with_collection_no_type()
    {
        $this->expectException(DataTransferObjectError::class);
        $this->expectExceptionMessage('Collection class `Spatie\DataTransferObject\Tests\DtoChildPropertyTypeCollectionWithNoType` has no defined array type.');

        $a = new DtoParentPropertyTypeWithCollectionNoType([
            'children' => [
                ['name' => 'test'],
                ['name' => 'test2'],
                ['name' => 'test3'],
            ],
        ]);
    }
}

// Scenario 1
class DtoParentPropertyType extends DataTransferObject
{
    public DtoChildPropertyTypeCollection $children;
}

class DtoChildPropertyType extends DataTransferObject
{
    public string $name;
}

class DtoChildPropertyTypeCollection extends DataTransferObjectCollection
{
    public function current(): DtoChildPropertyType
    {
        return parent::current();
    }
}

// Scenario 2
class DtoParentPropertyTypeUsingDocblock extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\DtoChildPropertyTypeCollection */
    public $children;
}

// Scenario 3
class DtoParentPropertyTypeWithCollectionDocblock extends DataTransferObject
{
    public DtoChildPropertyTypeCollectionWithDocblock $children;
}

/**
 * @method \Spatie\DataTransferObject\Tests\DtoChildPropertyType current
 */
class DtoChildPropertyTypeCollectionWithDocblock extends DataTransferObjectCollection
{
}

// Scenario 4
class DtoParentPropertyTypeWithCollectionNoType extends DataTransferObject
{
    public DtoChildPropertyTypeCollectionWithNoType $children;
}

class DtoChildPropertyTypeCollectionWithNoType extends DataTransferObjectCollection
{
}
