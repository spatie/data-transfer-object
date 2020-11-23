<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObjectWithCaster;

class CasterOnObjectTest extends TestCase
{
    /** @test */
    public function property_is_casted()
    {
        $dto = new class (
            complexObject: [
                'name' => 'test'
            ]
        ) extends DataTransferObject
        {
            public ComplexObjectWithCaster $complexObject;
        };

        $this->assertEquals('test', $dto->complexObject->name);
    }
}
