<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class BasicSnakeCaseDto extends DataTransferObject
{
    public string $name_field;
}
