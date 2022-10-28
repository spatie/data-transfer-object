<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

beforeAll(function () {
    class ScalarPropertyDto extends DataTransferObject
    {
        public int $foo;
    }
});

test('scalar property can be set', function () {
    $dto = new ScalarPropertyDto(foo: 1);

    $this->assertEquals(1, $dto->foo);
});
