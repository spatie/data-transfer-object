<?php

namespace Spatie\DataTransferObject;

interface DataTransformer
{
    public function transform(DataTransferObject $object, ...$args): void;

    /**
     * Args that must be forgotten after transformation.
     */
    public function argsToForget(): array;
}
