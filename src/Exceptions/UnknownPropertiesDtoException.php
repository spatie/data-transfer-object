<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.03.19
 * Time: 15:02
 */

namespace Spatie\DataTransferObject\Exceptions;

use TypeError;

class UnknownPropertiesDtoException extends TypeError
{
    public function __construct(array $properties, string $className)
    {
        $propertyNames = implode('`, `', $properties);

        parent::__construct("Public properties `{$propertyNames}` not found on {$className}");
    }
}
