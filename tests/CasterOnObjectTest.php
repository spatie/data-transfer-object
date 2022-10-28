<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObjectWithCaster;
use function PHPUnit\Framework\assertEquals;

test('property is casted', function () {
    $dto = new class (complexObject: [ 'name' => 'test' ]) extends DataTransferObject {
        public ComplexObjectWithCaster $complexObject;
    };

    assertEquals('test', $dto->complexObject->name);
});
