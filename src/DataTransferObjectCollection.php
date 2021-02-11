<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;

abstract class DataTransferObjectCollection implements
    ArrayAccess,
    Iterator,
    Countable
{
    protected ArrayIterator $collection;

    public function __construct(array $collection = [])
    {
        $this->collection = new ArrayIterator($collection);
    }

    public function current()
    {
        return $this->collection->current();
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

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->collection);
    }

    public function offsetUnset($offset)
    {
        unset($this->collection[$offset]);
    }

    public function next()
    {
        $this->collection->next();
    }

    public function key()
    {
        return $this->collection->key();
    }

    public function valid(): bool
    {
        return $this->collection->valid();
    }

    public function rewind()
    {
        $this->collection->rewind();
    }

    public function toArray(): array
    {
        $collection = $this->collection->getArrayCopy();

        foreach ($collection as $key => $item) {
            if (
                ! $item instanceof DataTransferObject
                && ! $item instanceof DataTransferObjectCollection
            ) {
                continue;
            }

            $collection[$key] = $item->toArray();
        }

        return $collection;
    }

    public function items(): array
    {
        return $this->collection->getArrayCopy();
    }

    public function count(): int
    {
        return count($this->collection);
    }
}
