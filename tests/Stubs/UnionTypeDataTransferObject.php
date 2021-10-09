<?php

namespace Spatie\DataTransferObject\Tests\Stubs;

use Spatie\DataTransferObject\DataTransferObject;

class UnionTypeDataTransferObject extends DataTransferObject
{
    public readonly string|SimpleDataTransferObject $person;

    public readonly string|int|float $amount;

    public readonly string $currency;
}
