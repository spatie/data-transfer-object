<?php

namespace Spatie\DataTransferObject\Casters;

use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;

class DataTransferObjectCaster implements Caster
{
    public function __construct(
        private string $className
    ) {
    }

    public function cast(mixed $value): DataTransferObject
    {
        if ($value instanceof $this->className) {
            return $value;
        }

        return new $this->className(...$value);
    }
}
