<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use InvalidArgumentException;
use Spatie\DataTransferObject\Uncaster;

class ComplexObjectUncaster implements Uncaster
{
    /**
     * @param array|mixed $value
     *
     * @return mixed
     */
    public function uncast($value): array
    {
        if (! $value instanceof ComplexObject) {
            throw new InvalidArgumentException('Cannot uncast an object that is not a ComplexObject');
        }

        return [
            'name' => $value->name
        ];
    }
}
