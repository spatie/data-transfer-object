<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use function PHPUnit\Framework\assertEquals;

beforeAll(function () {
    class ScalarPropertyDto extends DataTransferObject
    {
        public int $foo;
    }
});

test('scalar property can be set', function () {
    $dto = new ScalarPropertyDto(foo: 1);

    assertEquals(1, $dto->foo);
});
