<?php

namespace Spatie\DataTransferObject;

use Spatie\DataTransferObject\Descriptors\ClassDescriptor;
use Spatie\DataTransferObject\Resolvers\InboundMappedArgumentResolver;
use Spatie\DataTransferObject\Resolvers\InboundPropertyCastResolver;
use Spatie\DataTransferObject\Resolvers\InboundPropertyValueResolver;
use Spatie\DataTransferObject\Resolvers\InboundResolver;
use Spatie\DataTransferObject\Resolvers\OutboundResolver;

abstract class DataTransferObject
{
    private ClassDescriptor $descriptor;

    public static function new(...$arguments): static
    {
        return static::newWithoutValidation(...$arguments);
    }

    public static function newWithoutValidation(...$arguments): static
    {
        return (new static(...$arguments))
            ->resolve(new InboundMappedArgumentResolver())
            ->resolve(new InboundPropertyCastResolver())
            ->resolve(new InboundPropertyValueResolver());
    }

    private function __construct(...$arguments)
    {
        if (is_array($arguments[0] ?? null)) {
            $arguments = $arguments[0];
        }

        $this->descriptor = new ClassDescriptor($this, $arguments);
    }

    protected function resolve(InboundResolver|OutboundResolver $resolver): static
    {
        $resolver->resolve($this->descriptor);

        return $this;
    }
}
