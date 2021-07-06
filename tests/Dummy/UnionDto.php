<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class UnionDto extends DataTransferObject
{
    public string | int $foo;
}
