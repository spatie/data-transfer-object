<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class ScalarPropertyTest extends TestCase
{
    /** @test */
    public function scalar_property_can_be_set()
    {
        $dto = new class (
            foo: 1,
        ) extends DataTransferObject {
            public int $foo;
        };

        $this->assertEquals(1, $dto->foo);
    }
}
