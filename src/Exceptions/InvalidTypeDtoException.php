<?php

namespace Spatie\DataTransferObject\Exceptions;

use Spatie\DataTransferObject\Contracts\PropertyContract;
use TypeError;

class InvalidTypeDtoException extends TypeError
{
    public function __construct(PropertyContract $property, $value)
    {
        $value = $this->resolveTypeFromValue($value);

        $expectedTypes = $this->resolveExpectedTypes($property);

        parent::__construct("Invalid type: expected {$property->getFqn()} to be of type {$expectedTypes}, instead got value `{$value}`.");
    }

    protected function resolveTypeFromValue($value): string
    {
        if ($value === null) {
            $value = 'null';
        }

        if (is_object($value)) {
            $value = get_class($value);
        }

        if (is_array($value)) {
            $value = 'array';
        }

        return $value;
    }

    protected function resolveExpectedTypes(PropertyContract $property)
    {
        return implode(', ', $property->getTypes());
    }


}
