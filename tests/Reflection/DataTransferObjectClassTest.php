<?php

namespace Spatie\DataTransferObject\Tests\Reflection;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Reflection\DataTransferObjectClass;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

test('public properties', function () {
    $dto = new class () extends DataTransferObject {
        public $foo;

        public static $bar;

        private $baz;

        protected $boo;
    };

    $class = new DataTransferObjectClass($dto);

    assertCount(1, $class->getProperties());
    assertEquals('foo', $class->getProperties()[0]->name);
});
