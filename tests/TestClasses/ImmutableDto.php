<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Contracts\immutable;

class ImmutableDto extends DataTransferObject implements immutable
{
    /** @var string */
    public $name;
}
