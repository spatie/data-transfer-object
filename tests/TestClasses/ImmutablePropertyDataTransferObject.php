<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\immutable;

class ImmutablePropertyDataTransferObject extends DataTransferObject
{
    /** @var string|immutable */
    public $testProperty;
}
