<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\ValidationException;
use Spatie\DataTransferObject\Tests\Dummy\NumberBetween;
use function PHPUnit\Framework\assertEquals;

test('validation', function () {
    $dto = new class (foo: 50) extends DataTransferObject {
        #[NumberBetween(1, 100)]
        public int $foo;
    };

    assertEquals(50, $dto->foo);

    new class (foo: 150) extends DataTransferObject {
        #[NumberBetween(1, 100)]
        public int $foo;
    };
})->throws(ValidationException::class);
