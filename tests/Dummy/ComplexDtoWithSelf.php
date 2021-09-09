<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class ComplexDtoWithSelf extends DataTransferObject
{
    public string $name;

    public ?self $other;
}
