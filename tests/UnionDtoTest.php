<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class UnionDtoTest extends TestCase
{
    /** @test */
    public function union_types_are_allowed()
    {
        $dto = new UnionDto(foo: 1);

        $this->assertEquals(1, $dto->foo);
    }
}

class UnionDto extends DataTransferObject
{
    public string | int $foo;
}
