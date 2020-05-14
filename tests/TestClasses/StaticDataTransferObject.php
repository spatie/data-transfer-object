<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class StaticDataTransferObject extends DataTransferObject
{
    /** @var static|null */
    public $static;

    /** @var string */
    public $name;
}
