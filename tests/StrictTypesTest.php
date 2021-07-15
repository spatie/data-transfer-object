<?php
declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\Tests\Dummy\BasicArrayStrictTypeDto;
use Spatie\DataTransferObject\Tests\Dummy\BasicBooleanDto;
use Spatie\DataTransferObject\Tests\Dummy\BasicBooleanStrictTypeDto;
use Spatie\DataTransferObject\Tests\Dummy\BasicFloatDto;
use Spatie\DataTransferObject\Tests\Dummy\BasicFloatStrictTypeDto;
use Spatie\DataTransferObject\Tests\Dummy\BasicIntegerDto;
use Spatie\DataTransferObject\Tests\Dummy\BasicIntegerStrictTypeDto;
use Spatie\DataTransferObject\Tests\Dummy\BasicStringDto;
use Spatie\DataTransferObject\Tests\Dummy\BasicStringStrictTypeDto;
use Spatie\DataTransferObject\Tests\Dummy\ComplexStrictTypeDto;
use TypeError;

class StrictTypesTest extends TestCase
{
    /** @test */
    public function boolPropertyTypeStrictFailsTest()
    {
        $this->expectException(TypeError::class);
        $dto = new BasicBooleanStrictTypeDto(
            field : 2
        );
    }

    /** @test */
    public function boolPropertyTypeStrictSuccessTest()
    {
        $dto = new BasicBooleanStrictTypeDto(
            field : false
        );

        $this->assertEquals($dto->toArray(), ['field' => false]);
    }

    /** @test */
    public function complexBoolPropertyTypeStrictFailsTest()
    {
        $this->expectException(TypeError::class);
        $dto = new ComplexStrictTypeDto(
            field: ['field' => 2]
        );
    }

    /** @test */
    public function complexBoolPropertyTypeStrictSuccessTest()
    {
        $dto = new ComplexStrictTypeDto(
            field: ['field' => true]
        );

        $this->assertEquals($dto->toArray(), ['field' => ['field' => true]]);
    }

    /** @test */
    public function integerPropertyTypeStrictFailsTest()
    {
        $this->expectException(TypeError::class);
        $dto = new BasicIntegerStrictTypeDto(
            field : "2"
        );
    }

    /** @test */
    public function integerPropertyTypeStrictSuccessTest()
    {
        $dto = new BasicIntegerStrictTypeDto(
            field : 33
        );
        
        $this->assertEquals($dto->toArray(), ['field' => 33]);
    }

    /** @test */
    public function arrayPropertyTypeStrictFailsTest()
    {
        $this->expectException(TypeError::class);
        $dto = new BasicArrayStrictTypeDto(
            field : "text"
        );
    }

    /** @test */
    public function arrayPropertyTypeStrictSuccessTest()
    {
        $dto = new BasicArrayStrictTypeDto(
            field : [55]
        );
        
        $this->assertEquals($dto->toArray(), ['field' => [55]]);
    }

    /** @test */
    public function floatPropertyTypeStrictFailsTest()
    {
        $this->expectException(TypeError::class);
        $dto = new BasicFloatStrictTypeDto(
            field : "3"
        );
    }

    /** @test */
    public function floatPropertyTypeStrictSuccessTest()
    {
        $dto = new BasicFloatStrictTypeDto(
            field : 2.0
        );

        $this->assertEquals($dto->toArray(), ['field' => 2.0]);
    }

    /** @test */
    public function stringPropertyTypeStrictFailsTest()
    {
        $this->expectException(TypeError::class);
        $dto = new BasicStringStrictTypeDto(
            field : true
        );
    }

    /** @test */
    public function stringPropertyTypeStrictSuccessTest()
    {
        $dto = new BasicStringStrictTypeDto(
            field : "2.5"
        );

        $this->assertEquals($dto->toArray(), ['field' => 2.5]);
    }

    /** @test */
    public function boolPropertySuccessTest()
    {
        $dto = new BasicBooleanDto(
            field : 2
        );

        $this->assertEquals($dto->toArray(), ['field' => true]);
    }

    /** @test */
    public function floatPropertySuccessTest()
    {
        $dto = new BasicFloatDto(
            field : "2.2"
        );

        $this->assertEquals($dto->toArray(), ['field' => 2.2]);
    }

    /** @test */
    public function integerPropertySuccessTest()
    {
        $dto = new BasicIntegerDto(
            field : "66"
        );

        $this->assertEquals($dto->toArray(), ['field' => 66]);
    }

    /** @test */
    public function stringPropertySuccessTest()
    {
        $dto = new BasicStringDto(
            field : 100
        );

        $this->assertEquals($dto->toArray(), ['field' => "100"]);
    }
}
