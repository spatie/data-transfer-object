<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\Caster;

class RoundingCaster implements Caster
{
    public function cast(mixed $value): float
    {
        return round($value, 2);
    }
}
