<?php

namespace Spatie\DataTransferObject\Attributes;

use Attribute;
use JetBrains\PhpStorm\Immutable;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Spatie\DataTransferObject\Exceptions\InvalidUncasterClass;
use Spatie\DataTransferObject\Uncaster;

#[Attribute(Attribute::TARGET_CLASS)]
class DefaultUncast
{
    public function __construct(
        #[Immutable]
        private string $targetClass,
        #[Immutable]
        private string $uncasterClass,
    ) {
        if (! class_implements($this->uncasterClass, Uncaster::class)) {
            throw new InvalidUncasterClass($this->uncasterClass);
        }
    }

    public function accepts(ReflectionProperty $property): bool
    {
        $type = $property->getType();

        /** @var \ReflectionNamedType[]|null $types */
        $types = match ($type::class) {
            ReflectionNamedType::class => [$type],
            ReflectionUnionType::class => $type->getTypes(),
            default => null,
        };

        if (! $types) {
            return false;
        }

        foreach ($types as $type) {
            if ($type->getName() !== $this->targetClass) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function resolveUncaster(): Uncaster
    {
        return new $this->uncasterClass;
    }
}
