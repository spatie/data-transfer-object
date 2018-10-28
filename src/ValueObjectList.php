<?php

namespace Spatie\ValueObject;

use Iterator;
use Countable;
use ArrayAccess;

abstract class ValueObjectList implements
    ArrayAccess,
    Iterator,
    Countable
{
    /** @var array */
    protected $list;

    /** @var int */
    protected $position = 0;

    public function __construct(array $collection = [])
    {
        $this->list = $collection;
    }

    public function current()
    {
        return $this->list[$this->position];
    }

    public function offsetGet($offset): bool
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    public function offsetSet($offset, $value):void
    {
        if (is_null($offset)) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->list);
    }

    public function offsetUnset($offset): void
    {
        unset($this->list[$offset]);
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
        return array_key_exists($this->position, $this->list);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function toArray(): array
    {
        return $this->list;
    }

    public function count(): int
    {
        return count($this->list);
    }
}
