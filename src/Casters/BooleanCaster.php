<?php

namespace Spatie\DataTransferObject\Casters;

use Spatie\DataTransferObject\Caster;

class BooleanCaster implements Caster
{
    public function cast(mixed $value): bool
    {
        $acceptable = ['yes', 'on', '1', 1, true, 'true'];

        return in_array($value, $acceptable, true);
    }
}
