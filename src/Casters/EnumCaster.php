<?php

namespace Spatie\DataTransferObject\Casters;

use BackedEnum;
use LogicException;
use Spatie\DataTransferObject\Caster;

class EnumCaster implements Caster
{
    public function __construct(
        private array $types,
        private string $enumType
    ) {
    }

    public function cast(mixed $value): mixed
    {
        if (!is_subclass_of($this->enumType, BackedEnum::class)) {
            throw new LogicException("$this->enumType must be backed enum!");
        }

        $castedValue = $this->enumType::tryFrom($value);

        if ($castedValue === null) {
            throw new LogicException("Couldn't cast $this->enumType with value $value");
        }

        return $castedValue;
    }
}
