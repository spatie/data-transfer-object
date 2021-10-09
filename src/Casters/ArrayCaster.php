<?php

namespace Spatie\DataTransferObject\Casters;

use ArrayAccess;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;
use Spatie\DataTransferObject\Exceptions\CastingException;
use Traversable;

class ArrayCaster implements Caster
{
    public function __construct(private string $itemType)
    {
        //
    }

    public function cast(PropertyDescriptor $property, mixed $value): array|ArrayAccess
    {
        if ($property->hasType('array')) {
            return $this->mapInto(destination: [], items: $value);
        }

        if ($type = $property->getType(ArrayAccess::class)) {
            return $this->mapInto(destination: new ($type->getName()), items: $value);
        }

        throw CastingException::notCastableType(
            $this,
            $property->getTypeNames()->join(','),
            ['array', 'ArrayAccess']
        );
    }

    private function mapInto(array|ArrayAccess $destination, mixed $items): array|ArrayAccess
    {
        if ($destination instanceof ArrayAccess && is_subclass_of($destination, Traversable::class) === false) {
            throw CastingException::notCastable($this, 'ArrayAccess must be Traversable.');
        }

        foreach ($items as $key => $item) {
            $destination[$key] = $this->castItem($item);
        }

        return $destination;
    }

    private function castItem(mixed $item): mixed
    {
        switch ($this->itemType) {
            case 'string':
                return (string) $item;

            break;
            case 'float':
                return (float) $item;

            break;
            case 'int':
                return (int) $item;

            break;
            case 'array':
                return (array) $item;
        }

        if ($item instanceof $this->itemType) {
            return $item;
        }

        if (is_subclass_of($this->itemType, DataTransferObject::class)) {
            return $this->itemType::newWithoutValidation(...$item);
        }

        if (is_array($item)) {
            return new $this->itemType(...$item);
        }

        return new $this->itemType($item);
    }
}
