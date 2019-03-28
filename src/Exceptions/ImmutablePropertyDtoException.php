<?php
/**
 * Created by PhpStorm.
 * User: tony
 * Date: 28.03.19
 * Time: 15:02
 */

namespace Spatie\DataTransferObject\Exceptions;

use Spatie\DataTransferObject\Contracts\PropertyContract;
use TypeError;

class ImmutablePropertyDtoException extends TypeError
{
    public function __construct(string $property)
    {
        parent::__construct("Cannot change the value of property {$property}. It is immutable!");
    }
}
