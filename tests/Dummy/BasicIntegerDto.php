<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class BasicIntegerDto extends DataTransferObject
{
    public int $field;
}
