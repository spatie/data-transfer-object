<?php

namespace Spatie\DataTransferObject\Exceptions;

use Illuminate\Support\Collection;
use LogicException;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;

class UnknownProperties extends LogicException
{
    public static function new(ClassDescriptor $class, Collection $unknownProperties): static
    {
        $properties = $unknownProperties->count() > 1 ? 'properties' : 'property';

        return new static(
            "Data Transfer Object [{$class->getFqdn()}] does not have the referenced {$properties}: {$unknownProperties->toJson()}."
        );
    }
}
