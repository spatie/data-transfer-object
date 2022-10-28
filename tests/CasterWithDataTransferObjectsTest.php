<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use function PHPUnit\Framework\assertEquals;

beforeAll(function () {
    class DtoA extends DataTransferObject
    {
        public DtoB $dtoB;
    }

    class DtoB extends DataTransferObject
    {
        public DtoC $dtoC;
    }

    class DtoC extends DataTransferObject
    {
        public string $name;
    }
});

test('with nested dtos', function () {
    $dtoA = new DtoA([
        'dtoB' => [
            'dtoC' => [
                'name' => 'test',
            ],
        ],
    ]);

    assertEquals('test', $dtoA->dtoB->dtoC->name);
});
