<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\immutable;
use Spatie\DataTransferObject\DataTransferObject;

class ImmutableDto extends DataTransferObject implements immutable
{
    /** @var string */
    public $name;
}
