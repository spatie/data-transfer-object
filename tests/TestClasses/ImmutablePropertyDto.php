<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Contracts\Immutable;

class ImmutablePropertyDto extends DataTransferObject
{
    /** @var string|Immutable */
    public $immutableProperty;

    /** @var string */
    public $mutableProperty;
}
