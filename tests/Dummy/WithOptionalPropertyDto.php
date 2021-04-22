<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class WithOptionalPropertyDto extends DataTransferObject
{
    public ?string $name;
}
