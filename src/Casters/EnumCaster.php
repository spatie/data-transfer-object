<?php

namespace Spatie\DataTransferObject\Casters;

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
        return $this->enumType::from($value);
    }
}
