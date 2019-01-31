<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class NestedParentOfMany extends DataTransferObject
{
    /** @var \Spatie\DataTransferObject\Tests\TestClasses\NestedChild[] */
    public $children = [];

    /** @var string */
    public $name;
}
