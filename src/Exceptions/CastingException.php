<?php

namespace Spatie\DataTransferObject\Exceptions;

use LogicException;
use Spatie\DataTransferObject\Casters\Caster;

class CastingException extends LogicException
{
    public static function notCastable(Caster $caster, string $reason): static
    {
        $caster = $caster::class;

        return new static("Not castable by caster [{$caster}]. {$reason}");
    }

    public static function notCastableType(Caster $caster, string $type, array $castableTypes): static
    {
        $caster = $caster::class;
        $castableTypes = json_encode($castableTypes);

        return new static(
            "The provided type [$type] is not castable by caster [{$caster}]. Castable types are {$castableTypes}."
        );
    }
}
