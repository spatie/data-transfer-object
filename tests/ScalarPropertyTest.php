<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class ScalarPropertyTest extends TestCase
{
    /** @test */
    public function scalar_property_can_be_set()
    {
        $dto = ScalarPropertyDto::new(foo: 1);

        $this->assertEquals(1, $dto->foo);
    }
}

class ScalarPropertyDto extends DataTransferObject
{
    public int $foo;
}
