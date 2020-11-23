<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObjectCaster;

class CasterOnPropertyTest extends TestCase
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
            #[CastWith(ComplexObjectCaster::class)]
            public ComplexObject $complexObject;
        };

        $this->assertEquals('test', $dto->complexObject->name);
    }
}
