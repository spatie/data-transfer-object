<?php

namespace Spatie\DataTransferObject\Tests\Stubs;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;

#[Strict]
class StrictDataTransferObject extends DataTransferObject
{
    public readonly string $firstName;

    public readonly string $lastName;
}
