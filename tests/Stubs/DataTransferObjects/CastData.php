<?php

namespace Spatie\DataTransferObject\Tests\Stubs\DataTransferObjects;

use DateTime;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\Casters\DateTimeCaster;

class CastData extends DataTransferObject
{
    #[CastWith(DateTimeCaster::class)]
    public DateTime $date;
}
