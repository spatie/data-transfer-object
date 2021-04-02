<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class CasterWithDataTransferObjectsTest extends TestCase
{
    /** @test */
    public function test_with_nested_dtos()
    {
        $dtoA = new DtoA([
            'dtoB' => [
                'dtoC' => [
                    'name' => 'test',
                ],
            ],
        ]);

        $this->assertEquals('test', $dtoA->dtoB->dtoC->name);
    }
}

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
