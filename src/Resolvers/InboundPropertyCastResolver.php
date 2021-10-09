<?php

namespace Spatie\DataTransferObject\Resolvers;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;

class InboundPropertyCastResolver implements InboundResolver
{
    public function resolve(ClassDescriptor $descriptor): void
    {
        $descriptor->getProperties()->each(
            fn (PropertyDescriptor $property) => $this->castProperty($descriptor, $property)
        );
    }

    private function castProperty(ClassDescriptor $class, PropertyDescriptor $property): void
    {
        $castWith = $property->getAttribute(CastWith::class);
        $argument = $class->getArgument($property->getName());

        if (! $castWith || ! $argument) {
            return;
        }

        $class->setArgument(
            $property->getName(),
            $castWith->caster->cast($property, $argument)
        );
    }
}
