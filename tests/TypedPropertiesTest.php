<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class TypedPropertiesTest extends TestCase
{
    /** @test */
    public function test()
    {
        $dto = new TypedMyDTO([
            'typed' => 1,
            'docblock' => 'a',
        ]);

        $this->assertEquals(1, $dto->typed);
        $this->assertEquals('a', $dto->docblock);
    }
}

class TypedMyDTO extends DataTransferObject
{
    /** @var int */
    public $typed;

    /** @var string */
    public $docblock;
}
