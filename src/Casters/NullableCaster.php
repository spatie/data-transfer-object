<?php

namespace Spatie\DataTransferObject\Casters;

use Spatie\DataTransferObject\ImplicitCaster;

class NullableCaster implements ImplicitCaster
{
    public function __construct(
        private array $types,
        private mixed $default
    ) {
    }

    public function cast(mixed $value): mixed
    {
        return is_null($value) ? $this->default : $value;
    }
}
