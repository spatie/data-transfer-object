<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Contracts\Immutable;

class ImmutableNestedDto extends DataTransferObject implements Immutable
{
    /** @var string */
    public $name;

    /** @var \Spatie\DataTransferObject\Tests\TestClasses\NestedChild[]|array $child */
    public $children;
}
