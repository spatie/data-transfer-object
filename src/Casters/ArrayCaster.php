<?php

namespace Spatie\DataTransferObject\Casters;

use ArrayAccess;
use LogicException;
use Spatie\DataTransferObject\Caster;
use Traversable;

class ArrayCaster implements Caster
{
    public function __construct(
        private array $types,
        private string $itemType,
    ) {
    }

    public function cast(mixed $value): array | ArrayAccess
    {
        foreach ($this->types as $type) {
            if ($type == 'array') {
                return $this->mapInto(
                    destination: [],
                    items: $value
                );
            }

            if (is_subclass_of($type, ArrayAccess::class)) {
                return $this->mapInto(
                    destination: new $type(),
                    items: $value
                );
            }
        }

        throw new LogicException(
            "Caster [ArrayCaster] may only be used to cast arrays or objects that implement ArrayAccess."
        );
    }

    private function mapInto(array | ArrayAccess $destination, mixed $items): array | ArrayAccess
    {
        if ($destination instanceof ArrayAccess && ! is_subclass_of($destination, Traversable::class)) {
            throw new LogicException(
                "Caster [ArrayCaster] may only be used to cast ArrayAccess objects that are traversable."
            );
        }

        foreach ($items as $key => $item) {
            $destination[$key] = $this->castItem($item);
        }

        return $destination;
    }

    private function castItem(mixed $data)
    {
        if ($this->isSimpleType($this->itemType)) {
            return $this->castSimpleType($data);
        }

        if ($data instanceof $this->itemType) {
            return $data;
        }

        if (is_array($data)) {
            return new $this->itemType(...$data);
        }

        throw new LogicException(
            "Caster [ArrayCaster] each item must be an array or an instance of the specified item type [{$this->itemType}]."
        );
    }

    private function castSimpleType(mixed $data) {

        $dataType = gettype($data);

        if ($this->isSimpleType($dataType)) {
            settype($data, $this->itemType);
            return $data;
        }

        throw new LogicException(
            "Caster [ArrayCaster] given data type [{$dataType}] cannot be casted to [{$this->itemType}]"
        );
    }

    private function isSimpleType(string $type) {
        return in_array($type, [
            'boolean', 'bool',
            'integer', 'int',
            'float', 'double',
            'string',
            'NULL'
        ]);
    }

}
