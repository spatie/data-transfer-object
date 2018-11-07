<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class NestedParent extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\TestClasses\NestedChild */
    public $child;

    /** @var string */
    public $name;
}
