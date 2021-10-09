<?php

namespace Spatie\DataTransferObject\Casters;

use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;

interface Caster
{
    public function cast(PropertyDescriptor $property, mixed $value);
}
