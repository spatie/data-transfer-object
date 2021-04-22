<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\UncastWith;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObjectUncaster;

class UncasterOnPropertyTest extends TestCase
{
    /** @test */
    public function property_is_uncasted()
    {
        $array = [
            'complexObject' => [
                'name' => 'test',
            ],
        ];

        $complexObject = new ComplexObject('test');

        $dto = new class(complexObject: $complexObject) extends DataTransferObject {
            #[UncastWith(ComplexObjectUncaster::class)]
            public ComplexObject $complexObject;
        };

        $this->assertEquals($array, $dto->toArray());
    }
}
