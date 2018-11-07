<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class TestDataTransferObject extends DataTransferObject
{
    /** @var int */
    public $testProperty;
}
