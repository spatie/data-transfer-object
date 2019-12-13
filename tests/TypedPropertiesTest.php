<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class TypedPropertiesTest extends TestCase
{
    /** @test */
    public function test()
    {
        $dto = new MyDTO([
            'typed' => 1,
            'docblock' => 'a',
        ]);

        $this->assertEquals(1, $dto->typed);
        $this->assertEquals('a', $dto->docblock);
    }
}

class MyDTO extends DataTransferObject
{
    public int $typed;

    /** @var string */
    public $docblock;
}
