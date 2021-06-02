<?php

namespace Spatie\DataTransferObject\Casters;

use ArrayAccess;
use LogicException;
use Spatie\DataTransferObject\Caster;

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
                return $this->castArray($value);
            }

            if (is_subclass_of($type, ArrayAccess::class)) {
                return $this->castArrayAccess($type, $value);
            }
        }

        throw new LogicException("Caster [ArrayCaster] may only be used to cast arrays or objects that implement ArrayAccess.");
    }

    private function castArray(mixed $value): array
    {
        return array_map(
            fn (array $data) => new $this->itemType(...$data),
            $value
        );
    }

    private function castArrayAccess(string $type, mixed $value): ArrayAccess
    {
        $arrayAccess = new $type();

        array_walk(
            $value,
            fn (array $data) => $arrayAccess[] = new $this->itemType(...$data)
        );

        return $arrayAccess;
    }
}
