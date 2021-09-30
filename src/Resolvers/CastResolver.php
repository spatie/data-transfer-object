<?php

namespace Spatie\DataTransferObject\Resolvers;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;

class CastResolver
{
    public function __construct(private ClassDescriptor $descriptor)
    {
        //
    }

    public function castArguments(array $arguments): array
    {
        foreach ($arguments as $argument => $value) {
            $arguments[$argument] = $this->castArgument(
                $this->descriptor->getPropertyByName($argument),
                $value
            );
        }

        return $arguments;
    }

    protected function castArgument(PropertyDescriptor $property, mixed $value): mixed
    {
        $attributes = $property->getReflection()->getAttributes(CastWith::class);

        if (! count($attributes)) {
            return $value;
        }

        /** @var \Spatie\DataTransferObject\Attributes\CastWith $attribute */
        $attribute = $attributes[0]->newInstance();

        /** @var \Spatie\DataTransferObject\Casters\Caster $caster */
        $caster = new $attribute->casterClass($property, ...$attribute->args);

        return $caster->cast($value);
    }
}
