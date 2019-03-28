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

class UninitialisedPropertyDtoException extends TypeError
{
    public function __construct(PropertyContract $property)
    {
        parent::__construct("Non-nullable property {$property->getFqn()} has not been initialized.");
    }
}
