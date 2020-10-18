<?php

namespace Spatie\DataTransferObject;

class ValueCaster
{
    public function cast($value, FieldValidator $validator)
    {
        if (! $this->shouldBeCastToCollection($value)) {
            return $this->castValue($value, $validator->allowedTypes);
        }

        $values = $this->castCollection($value, $validator->allowedArrayTypes);
        $collectionType = $this->collectionType($validator->allowedTypes);

        return $collectionType ? new $collectionType($values) : $values;
    }

    public function castValue($value, array $allowedTypes)
    {
        $castTo = null;

        foreach ($allowedTypes as $type) {
            if (! is_subclass_of($type, DataTransferObject::class)) {
                continue;
            }

            $castTo = $type;

            break;
        }

        if (! $castTo) {
            return $value;
        }

        return new $castTo($value);
    }

    public function castCollection($values, array $allowedArrayTypes)
    {
        $castTo = null;

        foreach ($allowedArrayTypes as $type) {
            if (! is_subclass_of($type, DataTransferObject::class)) {
                continue;
            }

            $castTo = $type;

            break;
        }

        if (! $castTo) {
            return $values;
        }

        $casts = [];

        foreach ($values as $value) {
            $casts[] = new $castTo($value);
        }

        return $casts;
    }

    public function collectionType(array $types): string
    {
        foreach ($types as $type) {
            if (is_subclass_of($type, DataTransferObjectCollection::class)) {
                return $type;
            }
        }

        return false;
    }

    public function shouldBeCastToCollection(array $values): bool
    {
        if (empty($values)) {
            return false;
        }

        foreach ($values as $key => $value) {
            if (is_string($key)) {
                return false;
            }

            if (! is_array($value)) {
                return false;
            }
        }

        return true;
    }
}
