<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\PHPstan;

use PHPStan\Reflection\PropertyReflection;
use PHPStan\Rules\Properties\ReadWritePropertiesExtension;
use Spatie\DataTransferObject\DataTransferObject;

class PropertiesAreAlwaysInitializedExtension implements ReadWritePropertiesExtension
{
    public function isAlwaysRead(PropertyReflection $property, string $propertyName): bool
    {
        return false;
    }

    public function isAlwaysWritten(PropertyReflection $property, string $propertyName): bool
    {
        return false;
    }

    public function isInitialized(PropertyReflection $property, string $propertyName): bool
    {
        return $property->getDeclaringClass()->isSubclassOf(DataTransferObject::class);
    }
}
