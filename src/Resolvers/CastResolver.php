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
            $arguments[$argument] = $this->castAttribute(
                $this->descriptor->getPropertyByName($argument),
                $value
            );
        }

        return $arguments;
    }

    protected function castAttribute(PropertyDescriptor $property, mixed $value): mixed
    {
        $castAttributes = $property->getReflection()->getAttributes(CastWith::class);

        if (! count($castAttributes)) {
            return $value;
        }

        /** @var \Spatie\DataTransferObject\Attributes\CastWith $castAttribute */
        $castAttribute = $castAttributes[0]->newInstance();

        /** @var \Spatie\DataTransferObject\Casters\Caster $caster */
        $caster = new $castAttribute->casterClass($property, ...$castAttribute->args);

        return $caster->cast($value);
    }
}
