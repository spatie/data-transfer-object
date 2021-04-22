<?php

namespace Spatie\DataTransferObject\Exceptions;

use Exception;
use Spatie\DataTransferObject\Caster;
use Spatie\DataTransferObject\Uncaster;

class InvalidUncasterClass extends Exception
{
    public function __construct(string $className)
    {
        $expected = Uncaster::class;

        parent::__construct(
            "Class `{$className}` doesn't implement {$expected} and can't be used as a uncaster"
        );
    }
}
