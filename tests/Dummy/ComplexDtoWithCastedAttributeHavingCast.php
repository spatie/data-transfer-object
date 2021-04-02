<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class ComplexDtoWithCastedAttributeHavingCast extends DataTransferObject
{
    public string $name;

    public ComplexDtoWithCast $other;
}
