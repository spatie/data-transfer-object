<?php

namespace Spatie\DataTransferObject\Attributes;

use Attribute;
use JetBrains\PhpStorm\Immutable;
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

    public function resolveCaster(): Caster
    {
        return new $this->casterClass();
    }
}
