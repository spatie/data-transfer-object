<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class ComplexCamelCaseDto extends DataTransferObject
{
    public string $namePersonal;

    public BasicCamelCaseDto $otherField;
}
