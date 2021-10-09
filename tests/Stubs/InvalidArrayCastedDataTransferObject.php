<?php

namespace Spatie\DataTransferObject\Tests\Stubs;

use ArrayAccess;
use DateTime;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use Spatie\DataTransferObject\DataTransferObject;

class InvalidArrayCastedDataTransferObject extends DataTransferObject
{
    public readonly DateTime $array;

    public readonly ArrayAccessImplementation $anotherArray;
}

class ArrayAccessImplementation implements ArrayAccess
{
    public function __construct(private array $items = [])
    {
        //
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->items[$offset] = $value;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}
