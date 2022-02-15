<?php

namespace Spatie\DataTransferObject\Attributes;

use Attribute;
use Spatie\DataTransferObject\DataTransformer;
use Spatie\DataTransferObject\Exceptions\InvalidDataTransformerClass;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class TransformWith
{
    public function __construct(public string $dataTransformerClass) {
        if (! is_subclass_of($this->dataTransformerClass, DataTransformer::class)) {
            throw new InvalidDataTransformerClass($this->dataTransformerClass);
        }
    }
}
