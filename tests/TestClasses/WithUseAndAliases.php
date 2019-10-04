<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests\TestClasses;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\TestClasses\DummyClass as Dummy;

class WithUseAndAliases extends DataTransferObject
{
    /** @var EmptyClass */
    public $emptyClass;

    /** @var EmptyClass[] */
    public $arrayOfEmptyClasses;

    /** @var Dummy[] */
    public $arrayOfDummies;

    /** @var DummyClass[] */
    public $arrayOfDummyClasses;
}
