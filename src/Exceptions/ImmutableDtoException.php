<?php

namespace Spatie\DataTransferObject\Exceptions;

use TypeError;

class ImmutableDtoException extends TypeError
{
    public function __construct(string $property)
    {
        parent::__construct("Cannot change the value of property '{$property}' on an immutable data transfer object");
    }
}
