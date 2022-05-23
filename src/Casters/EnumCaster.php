<?php

namespace Spatie\DataTransferObject\Casters;

use LogicException;
use Spatie\DataTransferObject\Caster;

class EnumCaster implements Caster
{
    public function __construct(
        private array $types,
        private string $enumType,
        private string $enumValueType
    ) {
    }

    public function cast(mixed $value): mixed
    {
        if (gettype($value) !== $this->enumValueType) {
            throw new LogicException("$this->enumType can only be casted from $this->enumValueType");
        }

        return $this->enumType::from($value);
    }
}
