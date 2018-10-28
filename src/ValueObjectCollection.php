<?php

namespace Spatie\ValueObject;

use Iterator;
use Countable;
use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;

abstract class ValueObjectCollection implements
    ArrayAccess,
    Iterator,
    Countable,
    Arrayable
{
    /** @var array */
    protected $collection;

    /** @var int */
    protected $position = 0;

    public function __construct(array $collection = [])
    {
        $this->collection = $collection;
    }

    public function current(): array
    {
        return $this->collection[$this->position];
    }

    public function offsetGet($offset): bool
    {
        return isset($this->collection[$offset]) ? $this->collection[$offset] : null;
    }

    public function offsetSet($offset, $value): array
    {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetUnset($offset): void
    {
        unset($this->collection[$offset]);
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return array_key_exists($this->position, $this->collection);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function toArray(): array
    {
        return $this->collection;
    }

    public function count(): int
    {
        return count($this->collection);
    }
}
