<?php

namespace Spatie\DataTransferObject\Casters;

use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;

interface Caster
{
    public function __construct(PropertyDescriptor $descriptor);

    public function cast($value);
}
