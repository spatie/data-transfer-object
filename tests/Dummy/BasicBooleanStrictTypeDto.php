<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\Attributes\StrictType;
use Spatie\DataTransferObject\DataTransferObject;

#[StrictType]
class BasicBooleanStrictTypeDto extends DataTransferObject
{
    public bool $field;
}
