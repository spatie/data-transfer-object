<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class ComplexSnakeCaseDto extends DataTransferObject
{
    public string $name_personal;

    public BasicSnakeCaseDto $other_field;
}
