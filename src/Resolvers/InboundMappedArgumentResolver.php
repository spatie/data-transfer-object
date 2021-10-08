<?php

namespace Spatie\DataTransferObject\Resolvers;

use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;

class InboundMappedArgumentResolver implements InboundResolver
{
    public function resolve(ClassDescriptor $descriptor): void
    {
        $descriptor->getProperties()->each(
            fn ($property) => $this->resolvePropertyValue($property, $descriptor)
        );
    }

    private function resolvePropertyValue(PropertyDescriptor $property, ClassDescriptor $class): void
    {
        $mapFrom = $property->getAttribute(MapFrom::class)?->name;

        if ($mapFrom === null) {
            return;
        }

        $arguments = $class->getArguments();

        $arguments->put($property->getName(), $arguments->pull($mapFrom));

        // Prevents empty arrays from leaking into the arguments.
        $parentKey = strtok($mapFrom, '.');

        if ($arguments->has($parentKey) && is_array($arguments->get($parentKey))) {
            $arguments->forget($parentKey);
        }

        $class->setArguments($arguments);
    }
}
