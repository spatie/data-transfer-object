<?php

namespace Spatie\DataTransferObject;

class DataTransferObjectArray
{
    private array $attributes;

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function only(string ...$keys): DataTransferObjectArray
    {
        return new DataTransferObjectArray(Arr::only($this->attributes, $keys));
    }

    public function except(string ...$keys): DataTransferObjectArray
    {
        return new DataTransferObjectArray(Arr::except($this->attributes, $keys));
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
