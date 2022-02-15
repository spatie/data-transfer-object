<?php

namespace Spatie\DataTransferObject\Exceptions;

use Exception;
use Spatie\DataTransferObject\DataTransformer;

class InvalidDataTransformerClass extends Exception
{
    public function __construct(string $className)
    {
        $expected = DataTransformer::class;

        parent::__construct(
            "Class `{$className}` doesn't implement {$expected} and can't be used as a data transformer"
        );
    }
}
