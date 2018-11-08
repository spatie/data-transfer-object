<?php

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\TestClasses\DummyClass;

class ShorthandTypeDoc extends DataTransferObject
{
    /** @var DummyClass */
    public $var;
}
