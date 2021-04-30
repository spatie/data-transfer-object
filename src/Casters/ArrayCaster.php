<?php

namespace Spatie\DataTransferObject\Casters;

use Exception;
use Spatie\DataTransferObject\Caster;

class ArrayCaster implements Caster
{
    public function __construct(
        private string $type,
        private string $itemType,
    ) {
    }

    public function cast(mixed $value): array
    {
        if ($this->type !== 'array') {
            throw new Exception("Can only cast arrays");
        }

        return array_map(
            fn (array $data) => new $this->itemType(...$data),
            $value
        );
    }
}
