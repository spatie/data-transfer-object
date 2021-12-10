<?php

namespace Spatie\DataTransferObject\Attributes;

use Attribute;
use JetBrains\PhpStorm\Immutable;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Spatie\DataTransferObject\Caster;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class DefaultCast
{
    public function __construct(
        #[Immutable]
        private string $targetClass,
        #[Immutable]
        private string $casterClass,
    ) {
    }

    public function accepts(ReflectionProperty $property): bool
    {
        $type = $property->getType();

        if (is_null($type)) {
            return false;
        }

        if ($type instanceof ReflectionNamedType) {
            return $this->acceptsNamedType($type);
        }

        if ($type instanceof ReflectionUnionType) {
            return $this->acceptsUnionType($type);
        }

        if ($type instanceof ReflectionIntersectionType) {
            return $this->acceptsIntersectionType($type);
        }

        return false;
    }

    private function acceptsNamedType(ReflectionNamedType $named_type): bool
    {
        return $named_type->getName() === $this->targetClass;
    }

    private function acceptsUnionType(ReflectionUnionType $union_type): bool
    {
        foreach($union_type->getTypes() as $type) {
            if ($type === $this->targetClass) {
                return true;
            }
        }

        return false;
    }

    private function acceptsIntersectionType(ReflectionIntersectionType $intersection_type): bool
    {
        $targetClasses = explode('&', $this->targetClass);

        if (count($targetClasses) !== count($intersection_type->getTypes())) {
            return false;
        }

        foreach ($intersection_type->getTypes() as $type) {
            if (! in_array($type, $targetClasses)) {
                return false;
            }
        }

        return true;
    }

    public function resolveCaster(): Caster
    {
        return new $this->casterClass();
    }
}
