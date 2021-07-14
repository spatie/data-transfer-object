<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class BasicStringDto extends DataTransferObject
{
    public string $field;
}
