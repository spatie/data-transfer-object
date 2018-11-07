<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class NestedChild extends DataTransferObject
{
    /** @var string */
    public $name;
}
