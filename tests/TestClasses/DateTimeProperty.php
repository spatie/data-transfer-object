<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;

class DateTimeProperty extends DataTransferObject
{
    /** @var \DateTime */
    public $date;
}
