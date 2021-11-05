<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class CasterMethodOnObjectTest extends TestCase
{
    /** @test */
    public function property_is_casted_with_object_method()
    {
        $dto = new class(['someProperty' => 'test']) extends DataTransferObject {
            public string $someProperty;

            public function castSomeProperty(mixed $value)
            {
                return strtoupper($value);
            }
        };

        $this->assertEquals('TEST', $dto->someProperty);
    }
}
