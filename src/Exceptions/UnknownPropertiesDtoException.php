<?php

namespace Spatie\DataTransferObject\Exceptions;

use TypeError;

class UnknownPropertiesDtoException extends TypeError
{
    public function __construct(array $properties, string $className)
    {
        $propertyNames = implode('`, `', $properties);

        parent::__construct("Public properties `{$propertyNames}` not found on {$className}");
    }
}
