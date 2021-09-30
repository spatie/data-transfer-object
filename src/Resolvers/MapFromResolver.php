<?php

namespace Spatie\DataTransferObject\Resolvers;

use Spatie\DataTransferObject\Arr;
use Spatie\DataTransferObject\Attributes\MapFrom;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;

class MapFromResolver
{
    public function __construct(private ClassDescriptor $descriptor)
    {
        //
    }

    public function mapArguments(array $arguments): array
    {
        foreach ($this->descriptor->getProperties() as $property) {
            $arguments = $this->mapArgument($arguments, $property);
        }

        return $arguments;
    }

    private function mapArgument(array $arguments, PropertyDescriptor $property): array
    {
        $attributes = $property->getReflection()->getAttributes(MapFrom::class);

        if (empty($attributes)) {
            return $arguments;
        }

        $mapFromName = $attributes[0]->newInstance()->name;

        $arguments[$property->getName()] = (
            Arr::get($arguments, $mapFromName) ??
            $property->getReflection()->getDefaultValue()
        );

        return Arr::forget($arguments, $mapFromName);
    }
}
