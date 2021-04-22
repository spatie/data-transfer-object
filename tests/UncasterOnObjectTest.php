<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObjectWithUncaster;

class UncasterOnObjectTest extends TestCase
{
    /** @test */
    public function property_is_uncasted()
    {
        $array = [
            'complexObject' => [
                'name' => 'test',
            ],
        ];

        $complexObject = new ComplexObjectWithUncaster('test');

        $dto = new class(complexObject: $complexObject) extends DataTransferObject {
            public ComplexObjectWithUncaster $complexObject;
        };

        $this->assertEquals($array, $dto->toArray());
    }
}
