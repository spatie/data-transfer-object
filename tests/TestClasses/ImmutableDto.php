<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Contracts\Immutable;

class ImmutableDto extends DataTransferObject implements Immutable
{
    /** @var string */
    public $name;
}
