<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class NestedDataTransferObjectTest extends TestCase
{
    /** @test */
    public function with_typed_properties()
    {
        $a = new DtoAPropertyType([
            'b' => [
                'name' => 'test',
            ],
        ]);

        $this->assertEquals('test', $a->b->name);
    }
}

class DtoAPropertyType extends DataTransferObject
{
    public DtoBPropertyType $b;
}

class DtoBPropertyType extends DataTransferObject
{
    public string $name;
}
