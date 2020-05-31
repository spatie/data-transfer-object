<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectCollection;

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
        $this->markTestSkipped('@todo');

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
        $this->markTestSkipped('@todo');

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
    /** @var DtoChildPropertyTypeCollection */
    public $children;
}

// Scenario 3
class DtoParentPropertyTypeWithCollectionDocblock extends DataTransferObject
{
    public DtoChildPropertyTypeCollectionWithDocblock $children;
}

/**
 * @method PostData current
 */
class DtoChildPropertyTypeCollectionWithDocblock extends DataTransferObjectCollection
{
}
