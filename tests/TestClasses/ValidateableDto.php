<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Attribute;

class ValidateableDto extends DataTransferObject
{
    /** @var string */
    public $name;

    /** @var int */
    public $number;

    public function name(Attribute $attribute){
        return $attribute
            ->optional()
            ->rule("min:5")
            ->rule("max:30");
    }

    public function number(Attribute $attribute){
        return $attribute;
         //   ->constraint(function ($value){
          //      return $value>5;
           // });
    }
}
