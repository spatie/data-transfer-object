<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\DataTransferObject;

class BasicArrayDto extends DataTransferObject
{
    public array $field;
}
