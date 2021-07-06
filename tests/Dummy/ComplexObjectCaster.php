<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use Spatie\DataTransferObject\Caster;

class ComplexObjectCaster implements Caster
{
    /**
     * @param array|mixed $value
     *
     * @return mixed
     */
    public function cast(mixed $value): ComplexObject
    {
        if ($value instanceof ComplexObject) {
            return $value;
        }

        return new ComplexObject(
            name: $value['name']
        );
    }
}
