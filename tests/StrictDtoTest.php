<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class StrictDtoTest extends TestCase
{
    /** @test */
    public function non_strict_test()
    {
        $dto = NonStrictDto::new(
            name: 'name',
            unknown: 'unknown'
        );

        $this->markTestSucceeded();
    }

    /** @test */
    public function strict_test()
    {
        $this->expectException(UnknownProperties::class);

        $dto = StrictDto::new(
            name:    'name',
            unknown: 'unknown'
        );
    }

    /** @test */
    public function strict_child_test()
    {
        $this->expectException(UnknownProperties::class);

        $dto = ChildStrictDto::new(
            name: 'name',
            unknown: 'unknown'
        );
    }
}

#[Strict]
class StrictDto extends DataTransferObject
{
    public string $name;
}

final class ChildStrictDto extends StrictDto
{
}


class NonStrictDto extends DataTransferObject
{
    public string $name;
}
