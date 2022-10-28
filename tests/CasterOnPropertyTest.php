<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObject;
use Spatie\DataTransferObject\Tests\Dummy\ComplexObjectCaster;
use function PHPUnit\Framework\assertEquals;

test('property is casted', function () {
    $dto = new class (complexObject: [ 'name' => 'test' ]) extends DataTransferObject {
        #[CastWith(ComplexObjectCaster::class)]
        public ComplexObject $complexObject;
    };

    assertEquals('test', $dto->complexObject->name);
});
