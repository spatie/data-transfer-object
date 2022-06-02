<?php

namespace Spatie\DataTransferObject\Casters;

use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;

class EnumCaster implements Caster
{
    public function __construct(private string $enumType)
    {
    }

    public function cast(PropertyDescriptor $property, mixed $value): mixed
    {
        return $this->enumType::from($value);
    }
}
