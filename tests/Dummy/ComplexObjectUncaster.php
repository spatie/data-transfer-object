<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use InvalidArgumentException;
use Spatie\DataTransferObject\Uncaster;

class ComplexObjectUncaster implements Uncaster
{
    /**
     * @param ComplexObject $value
     *
     * @return array
     */
    public function uncast($value): array
    {
        if (! $value instanceof ComplexObject) {
            throw new InvalidArgumentException('Cannot uncast an object that is not a ComplexObject');
        }

        return [
            'name' => $value->name,
        ];
    }
}
