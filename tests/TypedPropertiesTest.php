<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\DataTransferObjectError;

class TypedPropertiesTest extends TestCase
{
    /** @test */
    public function test_with_typed_properties()
    {
        $dto = new TypedMyDTO([
            'typed' => 1,
            'docblock' => 'a',
        ]);

        $this->assertEquals(1, $dto->typed);
        $this->assertEquals('a', $dto->docblock);
    }

    /** @test */
    public function typed_properties_combined_with_docblocks()
    {
        $dto = new CombinedTypedDTO([
            'numbers' => [1, 2],
        ]);

        $this->assertInstanceOf(CombinedTypedDTO::class, $dto);

        $this->expectException(DataTransferObjectError::class);

        new CombinedTypedDTO([
            'numbers' => ['invalid', 'string'],
        ]);
    }
}

class TypedMyDTO extends DataTransferObject
{
    /** @var int */
    public $typed;

    /** @var string */
    public $docblock;
}

class CombinedTypedDTO extends DataTransferObject
{
    /** @var int[] */
    public array $numbers;
}
