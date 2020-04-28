<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DocblockFieldValidator;

class Foo
{
}
class FooChild extends Foo
{
}
class Bar
{
}

class DocblockFieldValidatorTest extends TestCase
{
    /** @test */
    public function nullable()
    {
        $this->assertTrue((new DocblockFieldValidator(''))->isNullable);
        $this->assertTrue((new DocblockFieldValidator('/**  */'))->isNullable);
        $this->assertTrue((new DocblockFieldValidator('/** @var string|null */'))->isNullable);
        $this->assertTrue((new DocblockFieldValidator('/** @var null */'))->isNullable);
        $this->assertTrue((new DocblockFieldValidator('/** @var mixed */'))->isNullable);

        $this->assertFalse((new DocblockFieldValidator('/** @var string */'))->isNullable);
    }

    /** @test */
    public function allowed_types()
    {
        $this->assertEquals(['string'], (new DocblockFieldValidator('/** @var string */'))->allowedTypes);
        $this->assertEquals(['\A\B'], (new DocblockFieldValidator('/** @var \A\B */'))->allowedTypes);
        $this->assertEquals(['string', 'integer'], (new DocblockFieldValidator('/** @var string|integer */'))->allowedTypes);
        $this->assertEquals(['string', 'integer'], (new DocblockFieldValidator('/** @var string|int */'))->allowedTypes);
        $this->assertEquals(['boolean'], (new DocblockFieldValidator('/** @var bool */'))->allowedTypes);
        $this->assertEquals(['double'], (new DocblockFieldValidator('/** @var float */'))->allowedTypes);
    }

    /** @test */
    public function allowed_array_types()
    {
        $this->assertEquals(['string'], (new DocblockFieldValidator('/** @var string[] */'))->allowedArrayTypes);
        $this->assertEquals(['\A\B'], (new DocblockFieldValidator('/** @var \A\B[] */'))->allowedArrayTypes);
        $this->assertEquals(['string', 'integer'], (new DocblockFieldValidator('/** @var string[]|int[] */'))->allowedArrayTypes);
        $this->assertEquals(['string'], (new DocblockFieldValidator('/** @var string[]|int */'))->allowedArrayTypes);
        $this->assertEquals(['string'], (new DocblockFieldValidator('/** @var iterable<string> */'))->allowedArrayTypes);
        $this->assertEquals(['string', 'integer'], (new DocblockFieldValidator('/** @var iterable<string>|int[] */'))->allowedArrayTypes);
    }

    /** @test */
    public function empty_type_is_always_valid()
    {
        $this->assertTrue((new DocblockFieldValidator())->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator())->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator())->isValidType(null));
    }

    /** @test */
    public function mixed_is_always_valid()
    {
        $this->assertTrue((new DocblockFieldValidator('/** @var mixed */'))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator('/** @var mixed */'))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator('/** @var mixed */'))->isValidType(null));
    }

    /** @test */
    public function nullable_types_are_validated()
    {
        $this->assertTrue((new DocblockFieldValidator())->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator('/**  */'))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator('/** @var string|null */'))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator('/** @var null */'))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator('/** @var mixed */'))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator('/** @var ?string */'))->isValidType(null));
    }

    /** @test */
    public function arrays_types_are_validated()
    {
        $this->assertTrue((new DocblockFieldValidator('/** @var string[] */'))->isValidType(['a']));
        $this->assertTrue((new DocblockFieldValidator('/** @var iterable<string> */'))->isValidType(['a']));

        $this->assertFalse((new DocblockFieldValidator('/** @var string[] */'))->isValidType([1]));
        $this->assertFalse((new DocblockFieldValidator('/** @var string[] */'))->isValidType('a'));
    }

    /** @test */
    public function any_type_of_array_or_iterable_is_allowed()
    {
        $this->assertTrue((new DocblockFieldValidator('/** @var array */'))->isValidType(['a', 1]));
        $this->assertTrue((new DocblockFieldValidator('/** @var iterable */'))->isValidType(['a', 1]));

        $this->assertFalse((new DocblockFieldValidator('/** @var string[] */'))->isValidType(['a', 1]));
        $this->assertFalse((new DocblockFieldValidator('/** @var iterable<string> */'))->isValidType(['a', 1]));
        $this->assertFalse((new DocblockFieldValidator('/** @var string[] */'))->isValidType(['a', 1]));
    }

    /** @test */
    public function types_are_validated()
    {
        $this->assertTrue((new DocblockFieldValidator('/** @var string */'))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator('/** @var float */'))->isValidType(1.0));
        $this->assertTrue((new DocblockFieldValidator('/** @var int */'))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator('/** @var int|float */'))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator('/** @var int|float */'))->isValidType(1.0));
        $this->assertTrue((new DocblockFieldValidator('/** @var int|string */'))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator('/** @var int|string */'))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator('/** @var string|null */'))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator('/** @var \Spatie\DataTransferObject\Tests\Foo */'))->isValidType(new Foo));
        $this->assertTrue((new DocblockFieldValidator('/** @var \Spatie\DataTransferObject\Tests\Foo */'))->isValidType(new FooChild));

        $this->assertFalse((new DocblockFieldValidator('/** @var string */'))->isValidType(1));
        $this->assertFalse((new DocblockFieldValidator('/** @var \Spatie\DataTransferObject\Tests\Foo */'))->isValidType(new Bar));
    }
}
