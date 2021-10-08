<?php

namespace Spatie\DataTransferObject\Tests\Stubs;

use Spatie\DataTransferObject\DataTransferObject;

class SimpleDataTransferObject extends DataTransferObject
{
    public readonly string $firstName;

    public readonly string $lastName;
}
