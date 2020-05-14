<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class SelfDataTransferObject extends DataTransferObject
{
    /** @var self|null */
    public $self;

    /** @var string */
    public $name;
}
