<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\immutable;
use Spatie\DataTransferObject\DataTransferObject;

class ImmutablePropertyDataTransferObject extends DataTransferObject
{
    /** @var string|immutable */
    public $testProperty;
}
