<?php

namespace Spatie\DataTransferObject\Exceptions;

use Exception;

class UnknownProperties extends Exception
{
    public static function new(string $dtoClass, array $fields): self
    {
        $properties = json_encode($fields);

        return new self("Unknown properties provided to `{$dtoClass}`: {$properties}");
    }
}
