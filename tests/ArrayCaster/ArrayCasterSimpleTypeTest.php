<?php

namespace Spatie\DataTransferObject\Tests\ArrayCaster;

use LogicException;
use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;
use stdClass;

class ArrayCasterSimpleTypeTest extends TestCase
{
    public function test_simple_type_casts() {

        $testData = ['2s','s3', 1, 5.9, '0.333', '.3', '0', null, false,];

        $foo = new ArraySimpleTypeCastExampleObject([
            'collectionOfInteger' => $testData,
            'collectionOfString' => $testData,
            'collectionOfFloat' => $testData,
        ]);

        foreach ($foo->collectionOfInteger as $integer) {
            $this->assertEquals('integer', gettype($integer));
        }
        $this->assertEquals([2, 0, 1, 5, 0, 0, 0, 0, 0], $foo->collectionOfInteger);

        foreach ($foo->collectionOfString as $string) {
            $this->assertEquals('string', gettype($string));
        }
        $this->assertEquals(['2s', 's3', '1', '5.9', '0.333', '.3', '0', '', ''], $foo->collectionOfString);

        foreach ($foo->collectionOfFloat as $float) {
            $this->assertEquals('double', gettype($float));
        }
        $this->assertEquals([2, 0, 1, 5.9, 0.333, 0.3, 0, 0, 0,], $foo->collectionOfFloat);
    }

    public function test_array_is_not_simple() {
        $this->expectException(LogicException::class);
        $this->expectErrorMessage('cannot be casted to [int]');
        new ArraySimpleTypeCastExampleObject([
            'collectionOfInteger' => [
                ['78'],
            ],
        ]);
    }

    public function test_object_is_not_simple() {
        $this->expectException(LogicException::class);
        $this->expectErrorMessage('cannot be casted to [int]');
        new ArraySimpleTypeCastExampleObject([
            'collectionOfInteger' => [
                new stdClass()
            ],
        ]);
    }
}


class ArraySimpleTypeCastExampleObject extends DataTransferObject {
    /** @var int[] */
    #[CastWith(ArrayCaster::class, 'int')]
    public array $collectionOfInteger = [];
    /** @var string[] */
    #[CastWith(ArrayCaster::class, 'string')]
    public array $collectionOfString = [];
    /** @var float[] */
    #[CastWith(ArrayCaster::class, 'float')]
    public array $collectionOfFloat = [];
}
