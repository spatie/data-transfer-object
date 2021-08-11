<?php

namespace Spatie\DataTransferObject\Attributes;

use Attribute;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\Exceptions\InvalidCasterClass;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class MapFrom
{
    public function __construct(
        public string | int $name,
    ) {
    }
}
