<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObjectWithCaster;

class CasterOnObjectTest extends TestCase
{
    /** @test */
    public function property_is_casted()
    {
        $dto = CasterOnObjectTestDTO::new(complexObject: ['name' => 'test']);

        $this->assertEquals('test', $dto->complexObject->name);
    }
}

class CasterOnObjectTestDTO extends DataTransferObject
{
    public ComplexObjectWithCaster $complexObject;
}
