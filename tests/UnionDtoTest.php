<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Tests\Dummy\UnionDto;

class UnionDtoTest extends TestCase
{
    /** @test */
    public function union_types_are_allowed()
    {
        $dto = new UnionDto(foo: 1);

        $this->assertEquals(1, $dto->foo);
    }
}
