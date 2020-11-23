<?php

namespace Spatie\DataTransferObject\Attributes;

use Attribute;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\Exceptions\InvalidCasterClass;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class CastWith
{
    public function __construct(
        public string $casterClass
    ) {
        if (! class_implements($this->casterClass, Caster::class)) {
            throw new InvalidCasterClass($this->casterClass);
        }
    }
}
