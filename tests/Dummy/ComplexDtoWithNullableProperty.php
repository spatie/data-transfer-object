<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class ComplexDtoWithNullableProperty extends DataTransferObject
{
    public string $name;

    public ?BasicDto $other;
}
