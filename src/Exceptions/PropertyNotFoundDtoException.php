<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.03.19
 * Time: 15:02
 */

namespace Spatie\DataTransferObject\Exceptions;

use TypeError;

class PropertyNotFoundDtoException extends TypeError
{
    public function __construct(string $property, string $className)
    {
        parent::__construct("Property `{$property}` not found on {$className}");
    }
}
