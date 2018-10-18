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

    public function offsetGet($offset)
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->list);
    }

    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
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
        return array_key_exists($this->position, $this->list);
    }

    public function rewind()
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
