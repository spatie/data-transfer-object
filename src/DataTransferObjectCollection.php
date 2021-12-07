<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Iterator;

/** @property array $collection */
abstract class DataTransferObjectCollection implements
    ArrayAccess,
    Iterator,
    Countable
{
    protected ArrayIterator $iterator;

    public function __construct(array $collection = [])
    {
        $this->iterator = new ArrayIterator($collection);
    }

    public function __get($name)
    {
        if ($name === 'collection') {
            return $this->iterator->getArrayCopy();
        }
    }

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->iterator->current();
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->iterator[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->iterator[] = $value;
        } else {
            $this->iterator[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return $this->iterator->offsetExists($offset);
    }

    public function offsetUnset($offset): void
    {
        unset($this->iterator[$offset]);
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function toArray(): array
    {
        $collection = $this->iterator->getArrayCopy();

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
        return $this->iterator->getArrayCopy();
    }

    public function count(): int
    {
        return count($this->iterator);
    }
}
