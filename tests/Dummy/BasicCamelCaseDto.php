<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class BasicCamelCaseDto extends DataTransferObject
{
    public string $nameField;
}
