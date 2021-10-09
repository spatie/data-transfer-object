<?php

namespace Spatie\DataTransferObject\Tests\Casters;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Collection;
use Spatie\DataTransferObject\Casters\ArrayCaster;
use Spatie\DataTransferObject\Exceptions\CastingException;
use Spatie\DataTransferObject\Tests\Stubs\ArrayCastedDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\InvalidArrayCastedDataTransferObject;
use Spatie\DataTransferObject\Tests\Stubs\SimpleDataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;

class ArrayCasterTest extends TestCase
{
    public function test_it_cannot_cast_anything_but_arrays_and_array_access()
    {
        $this->expectException(CastingException::class);
        $this->expectExceptionMessage(
            'The provided type [DateTime] is not castable by caster [Spatie\DataTransferObject\Casters\ArrayCaster]. Castable types are ["array","ArrayAccess"].'
        );

        $descriptor = $this->getDescriptor(InvalidArrayCastedDataTransferObject::class, [
            'array' => ['one', 'two', 'three'],
        ]);

        $caster = new ArrayCaster(itemType: 'string');
        $caster->cast(
            $descriptor->getProperty('array'),
            $descriptor->getArgument('array')
        );
    }

    public function test_it_cannot_cast_array_access_when_not_traversable(): void
    {
        $this->expectException(CastingException::class);
        $this->expectExceptionMessage(
            'Not castable by caster [Spatie\DataTransferObject\Casters\ArrayCaster]. ArrayAccess must be Traversable.'
        );

        $descriptor = $this->getDescriptor(InvalidArrayCastedDataTransferObject::class, [
            'anotherArray' => ['one', 'two', 'three'],
        ]);

        $caster = new ArrayCaster(itemType: 'string');
        $caster->cast(
            $descriptor->getProperty('anotherArray'),
            $descriptor->getArgument('anotherArray')
        );
    }

    public function test_it_can_cast_arrays_with_built_in_types(): void
    {
        $descriptor = $this->getDescriptor(ArrayCastedDataTransferObject::class, [
            'array' => [
                100,
                2000.50,
                '51000',
            ],
        ]);

        $intCaster = new ArrayCaster('int');
        $intResult = $intCaster->cast(
            $descriptor->getProperty('array'),
            $descriptor->getArgument('array')
        );

        $floatCaster = new ArrayCaster('float');
        $floatResult = $floatCaster->cast(
            $descriptor->getProperty('array'),
            $descriptor->getArgument('array')
        );

        $stringCaster = new ArrayCaster('string');
        $stringResult = $stringCaster->cast(
            $descriptor->getProperty('array'),
            $descriptor->getArgument('array')
        );

        foreach ($intResult as $int) {
            $this->assertIsInt($int);
        }

        foreach ($floatResult as $float) {
            $this->assertIsFloat($float);
        }

        foreach ($stringResult as $string) {
            $this->assertIsString($string);
        }
    }

    public function test_it_can_cast_arrays_with_simple_children_correctly(): void
    {
        $descriptor = $this->getDescriptor(ArrayCastedDataTransferObject::class, [
            'array' => [
                [ 'hi', 'ho' ],
                [ 'yo', 'lo' ],
            ],
        ]);

        $caster = new ArrayCaster('array');
        $result = $caster->cast(
            $descriptor->getProperty('array'),
            $descriptor->getArgument('array')
        );

        $this->assertSame(
            [[ 'hi', 'ho' ], [ 'yo', 'lo' ]],
            $result
        );
    }

    public function test_it_can_cast_arrays_with_data_transfer_object_children_correctly(): void
    {
        $descriptor = $this->getDescriptor(ArrayCastedDataTransferObject::class, [
            'personArray' => [
                [ 'firstName' => 'Clark', 'lastName' => 'Kent' ],
                SimpleDataTransferObject::new(firstName: 'Tony', lastName: 'Stark'),
            ],
        ]);

        $caster = new ArrayCaster(SimpleDataTransferObject::class);
        $result = $caster->cast(
            $descriptor->getProperty('personArray'),
            $descriptor->getArgument('personArray')
        );

        $this->assertIsArray($result);
        $this->assertContainsOnlyInstancesOf(SimpleDataTransferObject::class, $result);

        $this->assertSame($result[0]->firstName, 'Clark');
        $this->assertSame($result[0]->lastName, 'Kent');
        $this->assertSame($result[1]->firstName, 'Tony');
        $this->assertSame($result[1]->lastName, 'Stark');
    }

    public function test_it_can_cast_arrays_with_single_paramter_classes_as_the_item_type()
    {
        $descriptor = $this->getDescriptor(ArrayCastedDataTransferObject::class, [
            'dates' => [ '2021-01-01', '2021-01-02', '2021-01-03' ],
        ]);

        $caster = new ArrayCaster(DateTime::class);
        $result = $caster->cast(
            $descriptor->getProperty('dates'),
            $descriptor->getArgument('dates')
        );

        $this->assertContainsOnlyInstancesOf(DateTime::class, $result);
        $this->assertCount(3, $result);
    }

    public function test_it_can_cast_arrays_with_multi_parameter_classes_as_the_item_type()
    {
        $descriptor = $this->getDescriptor(ArrayCastedDataTransferObject::class, [
            'dates' => [
                ['2021-01-01', new DateTimeZone('Africa/Abidjan')],
                ['2021-01-02', new DateTimeZone('Antarctica/Casey')],
                ['2021-01-03', new DateTimeZone('Australia/Eucla')],
            ],
        ]);

        $caster = new ArrayCaster(DateTime::class);
        $result = $caster->cast(
            $descriptor->getProperty('dates'),
            $descriptor->getArgument('dates')
        );

        $this->assertContainsOnlyInstancesOf(DateTime::class, $result);
        $this->assertCount(3, $result);

        $this->assertEquals(new DateTimeZone('Africa/Abidjan'), $result[0]->getTimeZone());
        $this->assertEquals(new DateTimeZone('Antarctica/Casey'), $result[1]->getTimeZone());
        $this->assertEquals(new DateTimeZone('Australia/Eucla'), $result[2]->getTimeZone());
    }

    public function test_it_can_cast_traversable_array_access(): void
    {
        $descriptor = $this->getDescriptor(ArrayCastedDataTransferObject::class, [
            'otherPersonData' => [
                ['firstName' => 'Steve', 'lastName' => 'Rogers'],
                SimpleDataTransferObject::new(firstName: 'Bucky', lastName: 'Barnes'),
            ],
        ]);

        $caster = new ArrayCaster(SimpleDataTransferObject::class);
        $result = $caster->cast(
            $descriptor->getProperty('otherPersonData'),
            $descriptor->getArgument('otherPersonData')
        );

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertContainsOnlyInstancesOf(SimpleDataTransferObject::class, $result);
        $this->assertCount(2, $result);

        $this->assertSame('Steve', $result[0]->firstName);
        $this->assertSame('Rogers', $result[0]->lastName);
        $this->assertSame('Bucky', $result[1]->firstName);
        $this->assertSame('Barnes', $result[1]->lastName);
    }
}
