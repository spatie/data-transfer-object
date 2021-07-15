<?php

namespace Spatie\DataTransferObject\Casters;

use ArrayAccess;
use LogicException;
use Spatie\DataTransferObject\Caster;

class ArrayCaster implements Caster
{
    public function __construct(
        private string $type,
        private string $itemType,
    ) {
    }

    public function cast(mixed $value): array | ArrayAccess
    {
        if ($this->type == 'array') {
            return $this->castArray($value);
        }

        if (is_subclass_of($this->type, ArrayAccess::class)) {
            return $this->castArrayAccess($value);
        }

        throw new LogicException("Caster [ArrayCaster] may only be used to cast arrays or objects that implement ArrayAccess.");
    }

    private function castArray(array $value): array
    {
        return array_map([$this, 'makeItem'], $value);
    }

    private function castArrayAccess(array $value): ArrayAccess
    {
        $arrayAccess = new $this->type();

        foreach ($this->castArray($value) as $item) {
            $arrayAccess[] = $item;
        }

        return $arrayAccess;
    }

    private function makeItem(mixed $data): mixed
    {
        if ($data instanceof $this->itemType) {
            return $data;
        }

        if (is_array($data)) {
            return new $this->itemType(...$data);
        }

        throw new LogicException(
            'Caster [ArrayCaster] requires each item to be either an array or an instance of the specified class.'
        );
    }
}
