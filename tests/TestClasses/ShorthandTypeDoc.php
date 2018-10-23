<?php

namespace Spatie\ValueObject\Tests\TestClasses;

use Spatie\ValueObject\Tests\TestClasses\DummyClass;
use Spatie\ValueObject\ValueObject;

class ShorthandTypeDoc extends ValueObject
{
    /** @var DummyClass */
    public $var;
}
