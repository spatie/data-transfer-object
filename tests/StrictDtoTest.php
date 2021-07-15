<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class StrictDtoTest extends TestCase
{
    /** @test */
    public function non_strict_test()
    {
        $dto = new NonStrictDto(
            name: 'name',
            unknown: 'unknown'
        );

        $this->markTestSucceeded();
    }

    /** @test */
    public function strict_test()
    {
        $this->expectException(UnknownProperties::class);

        $dto = new StrictDto(
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

class NonStrictDto extends DataTransferObject
{
    public string $name;
}
