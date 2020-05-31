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

        $this->assertEquals('test', $a->bs[0]->name);
        $this->assertEquals('test2', $a->bs[2]->name);
        $this->assertEquals('test3', $a->bs[3]->name);
    }
}

class DtoParentPropertyType extends DataTransferObject
{
    public DtoChildCollectionType $children;
}

class DtoChildPropertyType extends DataTransferObject
{
    public string $name;
}

class DtoChildCollectionType extends DataTransferObjectCollection
{
    public function current(): DtoChildPropertyType
    {
        return parent::current();
    }
}
