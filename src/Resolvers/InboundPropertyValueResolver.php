<?php

namespace Spatie\DataTransferObject\Resolvers;

use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class InboundPropertyValueResolver implements InboundResolver
{
    private Collection $unknownProperties;

    public function __construct()
    {
        $this->unknownProperties = new Collection();
    }

    public function resolve(ClassDescriptor $descriptor): void
    {
        $descriptor->getArguments()->each(
            fn ($value, $argument) => $this->resolveArgumentToProperty($descriptor, $argument, $value)
        );

        if ($descriptor->isStrict() && $this->unknownProperties->isNotEmpty()) {
            throw UnknownProperties::new($descriptor, $this->unknownProperties);
        }
    }

    private function resolveArgumentToProperty(ClassDescriptor $descriptor, string $argument, mixed $value): void
    {
        $property = $descriptor->getProperty($argument);

        $property?->setValue($value) ?? $this->unknownProperties->add($argument);
    }
}
