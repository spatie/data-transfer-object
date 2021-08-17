<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class ComplexStrictDto extends DataTransferObject
{
    public string $name;

    public BasicDto $other;
}
