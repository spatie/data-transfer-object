<?php

namespace Spatie\DataTransferObject\Attributes;

use Attribute;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\Exceptions\InvalidCasterClass;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class CastWith
{
    public array $args;

    public function __construct(
        public string $casterClass,
        mixed ...$args
    ) {
        if (! is_subclass_of($this->casterClass, Caster::class)) {
            throw new InvalidCasterClass($this->casterClass);
        }

        $this->args = $args;
    }
}
