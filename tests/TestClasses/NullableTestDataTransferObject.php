<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class NullableTestDataTransferObject extends DataTransferObject
{
    /** @var string */
    public $foo = 'abc';

    /** @var bool|null */
    public $bar;
}
