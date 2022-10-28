<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Attributes\Strict;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

beforeAll(function () {
    #[Strict]
    class StrictDto extends DataTransferObject
    {
        public string $name;
    }

    final class ChildStrictDto extends StrictDto
    {
    }


    class NonStrictDto extends DataTransferObject
    {
        public string $name;
    }

});

test('non strict test', function () {
    $dto = new NonStrictDto(
        name: 'name',
        unknown: 'unknown'
    );

    $this->markTestSucceeded();
});

test('strict test', function () {
    $dto = new StrictDto(
        name:    'name',
        unknown: 'unknown'
    );
})->throws(UnknownProperties::class);

test('strict child test', function () {
    $dto = new ChildStrictDto(
        name: 'name',
        unknown: 'unknown'
    );
})->throws(UnknownProperties::class);
