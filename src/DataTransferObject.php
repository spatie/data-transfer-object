<?php

namespace Spatie\DataTransferObject;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\DataTransferObjectCaster;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Reflection\DataTransferObjectClass;

#[CastWith(DataTransferObjectCaster::class)]
abstract class DataTransferObject
{
    public function __construct(...$args)
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $class = new DataTransferObjectClass($this);

        foreach ($class->getProperties() as $property) {
            $property->setValue($args[$property->name] ?? null);

            unset($args[$property->name]);
        }

        if ($class->isStrict() && count($args)) {
            throw UnknownProperties::new(static::class, array_keys($args));
        }

        $class->validate();
    }
}
