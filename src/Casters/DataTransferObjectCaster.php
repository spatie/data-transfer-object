<?php

namespace Spatie\DataTransferObject\Casters;

use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\DataTransferObject;

class DataTransferObjectCaster implements Caster
{
    public function __construct(
        private array $classNames
    ) {
    }

    public function cast(mixed $value): DataTransferObject
    {
        foreach ($this->classNames as $className) {
            if ($value instanceof $className) {
                return $value;
            }
        }

        $className = $this->classNames[0];

        return call_user_func_array("{$className}::new", $value);
    }
}
