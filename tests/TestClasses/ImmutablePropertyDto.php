<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Contracts\immutable;

class ImmutablePropertyDto extends DataTransferObject
{
    /** @var string|immutable */
    public $immutableProperty;

    /** @var string */
    public $mutableProperty;
}
