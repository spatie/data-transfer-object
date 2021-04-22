<?php

namespace Spatie\DataTransferObject\Attributes;

use Attribute;
use Spatie\DataTransferObject\Uncaster;
use Spatie\DataTransferObject\Exceptions\InvalidUncasterClass;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class UncastWith
{
    public function __construct(
        public string $uncasterClass
    ) {
        if (! class_implements($this->uncasterClass, Uncaster::class)) {
            throw new InvalidUncasterClass($this->uncasterClass);
        }
    }
}
