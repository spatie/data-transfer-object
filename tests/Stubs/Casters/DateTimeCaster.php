<?php

namespace Spatie\DataTransferObject\Tests\Stubs\Casters;

use DateTime;
use LogicException;
use Spatie\DataTransferObject\Casters\Caster;
use Spatie\DataTransferObject\Descriptors\PropertyDescriptor;

class DateTimeCaster implements Caster
{
    public function __construct(private PropertyDescriptor $descriptor)
    {
        //
    }

    public function cast($value): DateTime
    {
        if (! $this->descriptor->hasType('DateTime')) {
            throw new LogicException('Unable to cast date time.');
        }

        return new DateTime($value);
    }
}
