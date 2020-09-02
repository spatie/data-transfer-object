<?php

namespace Spatie\DataTransferObject;

class ValueCaster
{
    public function cast($value, FieldValidator $validator)
    {
        return $this->shouldBeCastToCollection($value)
            ? $this->castCollection($value, $validator->allowedArrayTypes)
            : $this->castValue($value, $validator->allowedTypes);
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

        return (method_exists($castTo, 'fromRequest'))
            ? $castTo::fromRequest($value)
            : new $castTo($value);
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

        $use_fromRequest = method_exists($castTo, 'fromRequest');

        foreach ($values as $value) {
            if ($use_fromRequest) {
                $casts[] = $castTo::fromRequest($value);

                continue;
            }

            $casts[] = new $castTo($value);
        }

        return $casts;
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
