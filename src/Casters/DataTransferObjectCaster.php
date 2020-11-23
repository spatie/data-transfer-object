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
        return new $this->className(...$value);
    }
}
