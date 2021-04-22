<?php

namespace Spatie\DataTransferObject\Tests\Dummy;

use InvalidArgumentException;
use Spatie\DataTransferObject\Uncaster;

class ComplexObjectWithUncasterUncaster implements Uncaster
{
    /**
     * @param ComplexObjectWithUncaster $value
     *
     * @return array
     */
    public function uncast($value): array
    {
        if (! $value instanceof ComplexObjectWithUncaster) {
            throw new InvalidArgumentException('Cannot uncast an object that is not a ComplexObjectWithUncaster');
        }

        return [
            'name' => $value->name,
        ];
    }
}
