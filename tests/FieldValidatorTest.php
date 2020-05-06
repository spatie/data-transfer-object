<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\FieldValidator;

class Foo
{
}
class FooChild extends Foo
{
}
class Bar
{
}
class FooBar
{
    public string $string;
    public int $int;
    public bool $bool;
    public array $array;
    public ?string $nullable;
    public Foo $object;
    public $untyped;
}

class FieldValidatorTest extends TestCase
{
    /** @test */
    public function nullable()
    {
        $this->assertTrue((new FieldValidator())->isNullable);
        $this->assertTrue((new FieldValidator('/**  */'))->isNullable);
        $this->assertTrue((new FieldValidator('/** @var string|null */'))->isNullable);
        $this->assertTrue((new FieldValidator('/** @var null */'))->isNullable);
        $this->assertTrue((new FieldValidator('/** @var mixed */'))->isNullable);

        $this->assertFalse((new FieldValidator('/** @var string */'))->isNullable);

        $reflection = new \ReflectionClass(FooBar::class);
        $this->assertFalse((new FieldValidator($reflection->getProperty('string')))->isNullable);
        $this->assertFalse((new FieldValidator($reflection->getProperty('int')))->isNullable);
        $this->assertFalse((new FieldValidator($reflection->getProperty('bool')))->isNullable);
        $this->assertFalse((new FieldValidator($reflection->getProperty('array')))->isNullable);
        $this->assertTrue((new FieldValidator($reflection->getProperty('nullable')))->isNullable);
        $this->assertFalse((new FieldValidator($reflection->getProperty('object')))->isNullable);
    }

    /** @test */
    public function allowed_types()
    {
        $this->assertEquals(['string'], (new FieldValidator('/** @var string */'))->allowedTypes);
        $this->assertEquals(['\A\B'], (new FieldValidator('/** @var \A\B */'))->allowedTypes);
        $this->assertEquals(['string', 'integer'], (new FieldValidator('/** @var string|integer */'))->allowedTypes);
        $this->assertEquals(['string', 'integer'], (new FieldValidator('/** @var string|int */'))->allowedTypes);
        $this->assertEquals(['boolean'], (new FieldValidator('/** @var bool */'))->allowedTypes);
        $this->assertEquals(['double'], (new FieldValidator('/** @var float */'))->allowedTypes);

        $reflection = new \ReflectionClass(FooBar::class);
        $this->assertEquals(['string'], (new FieldValidator($reflection->getProperty('string')))->allowedTypes);
        $this->assertEquals(['integer'], (new FieldValidator($reflection->getProperty('int')))->allowedTypes);
        $this->assertEquals(['boolean'], (new FieldValidator($reflection->getProperty('bool')))->allowedTypes);
        $this->assertEquals(['array'], (new FieldValidator($reflection->getProperty('array')))->allowedTypes);
        $this->assertEquals(['string'], (new FieldValidator($reflection->getProperty('nullable')))->allowedTypes);
        $this->assertEquals([Foo::class], (new FieldValidator($reflection->getProperty('object')))->allowedTypes);
    }

    /** @test */
    public function allowed_array_types()
    {
        $this->assertEquals(['string'], (new FieldValidator('/** @var string[] */'))->allowedArrayTypes);
        $this->assertEquals(['\A\B'], (new FieldValidator('/** @var \A\B[] */'))->allowedArrayTypes);
        $this->assertEquals(['string', 'integer'], (new FieldValidator('/** @var string[]|int[] */'))->allowedArrayTypes);
        $this->assertEquals(['string'], (new FieldValidator('/** @var string[]|int */'))->allowedArrayTypes);
        $this->assertEquals(['string'], (new FieldValidator('/** @var iterable<string> */'))->allowedArrayTypes);
        $this->assertEquals(['string', 'integer'], (new FieldValidator('/** @var iterable<string>|int[] */'))->allowedArrayTypes);

        $reflection = new \ReflectionClass(FooBar::class);
        $this->assertEquals([], (new FieldValidator($reflection->getProperty('string')))->allowedArrayTypes);
        $this->assertEquals([], (new FieldValidator($reflection->getProperty('int')))->allowedArrayTypes);
        $this->assertEquals([], (new FieldValidator($reflection->getProperty('bool')))->allowedArrayTypes);
        $this->assertEquals([], (new FieldValidator($reflection->getProperty('array')))->allowedArrayTypes);
        $this->assertEquals([], (new FieldValidator($reflection->getProperty('nullable')))->allowedArrayTypes);
        $this->assertEquals([], (new FieldValidator($reflection->getProperty('object')))->allowedArrayTypes);
    }

    /** @test */
    public function empty_type_is_always_valid()
    {
        $this->assertTrue((new FieldValidator())->isValidType(1));
        $this->assertTrue((new FieldValidator())->isValidType('a'));
        $this->assertTrue((new FieldValidator())->isValidType(null));

        $reflection = new \ReflectionClass(FooBar::class);
        $this->assertTrue((new FieldValidator($reflection->getProperty('untyped')))->isValidType(1));
        $this->assertTrue((new FieldValidator($reflection->getProperty('untyped')))->isValidType('a'));
        $this->assertTrue((new FieldValidator($reflection->getProperty('untyped')))->isValidType(null));
    }

    /** @test */
    public function mixed_is_always_valid()
    {
        $this->assertTrue((new FieldValidator('/** @var mixed */'))->isValidType(1));
        $this->assertTrue((new FieldValidator('/** @var mixed */'))->isValidType('a'));
        $this->assertTrue((new FieldValidator('/** @var mixed */'))->isValidType(null));
    }

    /** @test */
    public function nullable_types_are_validated()
    {
        $this->assertTrue((new FieldValidator())->isValidType(null));
        $this->assertTrue((new FieldValidator('/**  */'))->isValidType(null));
        $this->assertTrue((new FieldValidator('/** @var string|null */'))->isValidType(null));
        $this->assertTrue((new FieldValidator('/** @var null */'))->isValidType(null));
        $this->assertTrue((new FieldValidator('/** @var mixed */'))->isValidType(null));
        $this->assertTrue((new FieldValidator('/** @var ?string */'))->isValidType(null));
    }

    /** @test */
    public function arrays_types_are_validated()
    {
        $this->assertTrue((new FieldValidator('/** @var string[] */'))->isValidType(['a']));
        $this->assertTrue((new FieldValidator('/** @var iterable<string> */'))->isValidType(['a']));

        $this->assertFalse((new FieldValidator('/** @var string[] */'))->isValidType([1]));
        $this->assertFalse((new FieldValidator('/** @var string[] */'))->isValidType('a'));
    }

    /** @test */
    public function any_type_of_array_or_iterable_is_allowed()
    {
        $this->assertTrue((new FieldValidator('/** @var array */'))->isValidType(['a', 1]));
        $this->assertTrue((new FieldValidator('/** @var iterable */'))->isValidType(['a', 1]));

        $this->assertFalse((new FieldValidator('/** @var string[] */'))->isValidType(['a', 1]));
        $this->assertFalse((new FieldValidator('/** @var iterable<string> */'))->isValidType(['a', 1]));
        $this->assertFalse((new FieldValidator('/** @var string[] */'))->isValidType(['a', 1]));

        $reflection = new \ReflectionClass(FooBar::class);
        $this->assertFalse((new FieldValidator($reflection->getProperty('string')))->isValidType(['a', 1]));
        $this->assertFalse((new FieldValidator($reflection->getProperty('int')))->isValidType(['a', 1]));
        $this->assertFalse((new FieldValidator($reflection->getProperty('bool')))->isValidType(['a', 1]));
        $this->assertTrue((new FieldValidator($reflection->getProperty('array')))->isValidType(['a', 1]));
        $this->assertFalse((new FieldValidator($reflection->getProperty('object')))->isValidType(['a', 1]));
    }

    /** @test */
    public function types_are_validated()
    {
        $this->assertTrue((new FieldValidator('/** @var string */'))->isValidType('a'));
        $this->assertTrue((new FieldValidator('/** @var float */'))->isValidType(1.0));
        $this->assertTrue((new FieldValidator('/** @var int */'))->isValidType(1));
        $this->assertTrue((new FieldValidator('/** @var int|float */'))->isValidType(1));
        $this->assertTrue((new FieldValidator('/** @var int|float */'))->isValidType(1.0));
        $this->assertTrue((new FieldValidator('/** @var int|string */'))->isValidType(1));
        $this->assertTrue((new FieldValidator('/** @var int|string */'))->isValidType('a'));
        $this->assertTrue((new FieldValidator('/** @var string|null */'))->isValidType('a'));
        $this->assertTrue((new FieldValidator('/** @var \Spatie\DataTransferObject\Tests\Foo */'))->isValidType(new Foo));
        $this->assertTrue((new FieldValidator('/** @var \Spatie\DataTransferObject\Tests\Foo */'))->isValidType(new FooChild));

        $this->assertFalse((new FieldValidator('/** @var string */'))->isValidType(1));
        $this->assertFalse((new FieldValidator('/** @var \Spatie\DataTransferObject\Tests\Foo */'))->isValidType(new Bar));

        $reflection = new \ReflectionClass(FooBar::class);
        $this->assertTrue((new FieldValidator($reflection->getProperty('string')))->isValidType('a'));
        $this->assertTrue((new FieldValidator($reflection->getProperty('int')))->isValidType(1));
        $this->assertTrue((new FieldValidator($reflection->getProperty('bool')))->isValidType(true));
        $this->assertTrue((new FieldValidator($reflection->getProperty('array')))->isValidType([1, 2]));
        $this->assertTrue((new FieldValidator($reflection->getProperty('nullable')))->isValidType(null));
        $this->assertTrue((new FieldValidator($reflection->getProperty('object')))->isValidType(new Foo()));

        $this->assertFalse((new FieldValidator($reflection->getProperty('object')))->isValidType('foo'));
        $this->assertFalse((new FieldValidator($reflection->getProperty('string')))->isValidType(1));
    }
}
