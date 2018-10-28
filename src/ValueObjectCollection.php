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

    public function current()
    {
        return $this->collection[$this->position];
    }

    public function offsetGet($offset)
    {
        return $this->collection[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->collection[] = $value;
        } else {
            $this->collection[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    public function next()
    {
        $this->position++;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return array_key_exists($this->position, $this->collection);
    }

    public function rewind()
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
