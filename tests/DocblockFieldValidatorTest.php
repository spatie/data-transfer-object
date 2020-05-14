<?php

declare(strict_types=1);

namespace Spatie\DataTransferObject\Tests;

use ReflectionClass;
use Spatie\DataTransferObject\DocblockFieldValidator;

class Foo
{
    /** @var self */
    public self $self;
    /** @var static */
    public $static;
    /** @var self[] */
    public $selfArray;
    /** @var static[] */
    public $staticArray;
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
        [$class, $noDocBlock, $emptyDocBlock, $stringOrNull, $null, $mixed, $string] = $this->getClassAndProperties(new class() {
            public $noDocBlock;
            /**  */
            public $emptyDocBlock;
            /** @var string|null */
            public $stringOrNull;
            /** @var null */
            public $null;
            /** @var mixed */
            public $mixed;
            /** @var string */
            public $string;
        });

        $this->assertTrue((new DocblockFieldValidator('', $noDocBlock, $class))->isNullable);
        $this->assertTrue((new DocblockFieldValidator($emptyDocBlock->getDocComment(), $emptyDocBlock, $class))->isNullable);
        $this->assertTrue((new DocblockFieldValidator($stringOrNull->getDocComment(), $stringOrNull, $class))->isNullable);
        $this->assertTrue((new DocblockFieldValidator($null->getDocComment(), $null, $class))->isNullable);
        $this->assertTrue((new DocblockFieldValidator($mixed->getDocComment(), $mixed, $class))->isNullable);

        $this->assertFalse((new DocblockFieldValidator($string->getDocComment(), $string, $class))->isNullable);
    }

    /** @test */
    public function allowed_types()
    {
        [$class, $string, $b, $stringOrInteger, $stringOrInt, $bool, $float] = $this->getClassAndProperties(new class() {
            /** @var string */
            public $string;
            /** @var \A\B */
            public $b;
            /** @var string|integer */
            public $stringOrInteger;
            /** @var string|int */
            public $stringOrInt;
            /** @var bool */
            public $bool;
            /** @var float */
            public $float;
        });

        $this->assertEquals(['string'], (new DocblockFieldValidator($string->getDocComment(), $string, $class))->allowedTypes);
        $this->assertEquals(['\A\B'], (new DocblockFieldValidator($b->getDocComment(), $b, $class))->allowedTypes);
        $this->assertEquals(['string', 'integer'], (new DocblockFieldValidator($stringOrInteger->getDocComment(), $stringOrInteger, $class))->allowedTypes);
        $this->assertEquals(['string', 'integer'], (new DocblockFieldValidator($stringOrInt->getDocComment(), $stringOrInt, $class))->allowedTypes);
        $this->assertEquals(['boolean'], (new DocblockFieldValidator($bool->getDocComment(), $bool, $class))->allowedTypes);
        $this->assertEquals(['double'], (new DocblockFieldValidator($float->getDocComment(), $float, $class))->allowedTypes);
    }

    /** @test */
    public function allowed_array_types()
    {
        [$class, $stringArray, $bArray, $stringArrayOrIntArray, $stringArrayOrInt, $stringIterable, $iterableStringOrIntArray] = $this->getClassAndProperties(new class() {
            /** @var string[] */
            public $stringArray;
            /** @var \A\B[] */
            public $bArray;
            /** @var string[]|int[] */
            public $stringArrayOrIntArray;
            /** @var string[]|int */
            public $stringArrayOrInt;
            /** @var iterable<string> */
            public $stringIterable;
            /** @var iterable<string>|int[] */
            public $iterableStringOrIntArray;
        });

        $this->assertEquals(['string'], (new DocblockFieldValidator($stringArray->getDocComment(), $stringArray, $class))->allowedArrayTypes);
        $this->assertEquals(['\A\B'], (new DocblockFieldValidator($bArray->getDocComment(), $bArray, $class))->allowedArrayTypes);
        $this->assertEquals(['string', 'integer'], (new DocblockFieldValidator($stringArrayOrIntArray->getDocComment(), $stringArrayOrIntArray, $class))->allowedArrayTypes);
        $this->assertEquals(['string'], (new DocblockFieldValidator($stringArrayOrInt->getDocComment(), $stringArrayOrInt, $class))->allowedArrayTypes);
        $this->assertEquals(['string'], (new DocblockFieldValidator($stringIterable->getDocComment(), $stringIterable, $class))->allowedArrayTypes);
        $this->assertEquals(['string', 'integer'], (new DocblockFieldValidator($iterableStringOrIntArray->getDocComment(), $iterableStringOrIntArray, $class))->allowedArrayTypes);
    }

    /** @test */
    public function empty_type_is_always_valid()
    {
        [$class, $empty] = $this->getClassAndProperties(new class() {
            public $empty;
        });

        $this->assertTrue((new DocblockFieldValidator('', $empty, $class))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator('', $empty, $class))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator('', $empty, $class))->isValidType(null));
    }

    /** @test */
    public function mixed_is_always_valid()
    {
        [$class, $mixed] = $this->getClassAndProperties(new class() {
            /** @var mixed */
            public $mixed;
        });

        $this->assertTrue((new DocblockFieldValidator($mixed->getDocComment(), $mixed, $class))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator($mixed->getDocComment(), $mixed, $class))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator($mixed->getDocComment(), $mixed, $class))->isValidType(null));
    }

    /** @test */
    public function nullable_types_are_validated()
    {
        [$class, $noDocBlock, $emptyDocBlock, $stringOrNull, $null, $mixed, $nullableString] = $this->getClassAndProperties(new class() {
            public $noDocBlock;
            /**  */
            public $emptyDocBlock;
            /** @var string|null */
            public $stringOrNull;
            /** @var null */
            public $null;
            /** @var mixed */
            public $mixed;
            /** @var ?string */
            public $nullableString;
        });

        $this->assertTrue((new DocblockFieldValidator('', $noDocBlock, $class))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator($emptyDocBlock->getDocComment(), $emptyDocBlock, $class))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator($stringOrNull->getDocComment(), $stringOrNull, $class))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator($null->getDocComment(), $null, $class))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator($mixed->getDocComment(), $mixed, $class))->isValidType(null));
        $this->assertTrue((new DocblockFieldValidator($nullableString->getDocComment(), $nullableString, $class))->isValidType(null));
    }

    /** @test */
    public function arrays_types_are_validated()
    {
        [$class, $stringArray, $stringIterable] = $this->getClassAndProperties(new class() {
            /** @var string[] */
            public $stringArray;
            /** @var iterable<string> */
            public $stringIterable;
        });

        $this->assertTrue((new DocblockFieldValidator($stringArray->getDocComment(), $stringArray, $class))->isValidType(['a']));
        $this->assertTrue((new DocblockFieldValidator($stringIterable->getDocComment(), $stringIterable, $class))->isValidType(['a']));

        $this->assertFalse((new DocblockFieldValidator($stringArray->getDocComment(), $stringArray, $class))->isValidType([1]));
        $this->assertFalse((new DocblockFieldValidator($stringArray->getDocComment(), $stringArray, $class))->isValidType('a'));
    }

    /** @test */
    public function any_type_of_array_or_iterable_is_allowed()
    {
        [$class, $array, $iterable, $stringArray, $stringIterable] = $this->getClassAndProperties(new class() {
            /** @var array */
            public $array;
            /** @var iterable */
            public $iterable;
            /** @var string[] */
            public $stringArray;
            /** @var iterable<string> */
            public $stringIterable;
        });

        $this->assertTrue((new DocblockFieldValidator($array->getDocComment(), $array, $class))->isValidType(['a', 1]));
        $this->assertTrue((new DocblockFieldValidator($iterable->getDocComment(), $iterable, $class))->isValidType(['a', 1]));

        $this->assertFalse((new DocblockFieldValidator($stringArray->getDocComment(), $stringArray, $class))->isValidType(['a', 1]));
        $this->assertFalse((new DocblockFieldValidator($stringIterable->getDocComment(), $stringIterable, $class))->isValidType(['a', 1]));
        $this->assertFalse((new DocblockFieldValidator($stringArray->getDocComment(), $stringArray, $class))->isValidType(['a', 1]));
    }

    /** @test */
    public function types_are_validated()
    {
        [$class, $string, $float, $int, $intOrFloat, $intOrString, $stringOrNull, $foo] = $this->getClassAndProperties(new class() {
            /** @var string */
            public $string;
            /** @var float */
            public $float;
            /** @var int */
            public $int;
            /** @var int|float */
            public $intOrFloat;
            /** @var int|string */
            public $intOrString;
            /** @var string|null */
            public $stringOrNull;
            /** @var \Spatie\DataTransferObject\Tests\Foo */
            public $foo;
        });

        $this->assertTrue((new DocblockFieldValidator($string->getDocComment(), $string, $class))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator($float->getDocComment(), $float, $class))->isValidType(1.0));
        $this->assertTrue((new DocblockFieldValidator($int->getDocComment(), $int, $class))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator($intOrFloat->getDocComment(), $intOrFloat, $class))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator($intOrFloat->getDocComment(), $intOrFloat, $class))->isValidType(1.0));
        $this->assertTrue((new DocblockFieldValidator($intOrString->getDocComment(), $intOrString, $class))->isValidType(1));
        $this->assertTrue((new DocblockFieldValidator($intOrString->getDocComment(), $intOrString, $class))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator($stringOrNull->getDocComment(), $stringOrNull, $class))->isValidType('a'));
        $this->assertTrue((new DocblockFieldValidator($foo->getDocComment(), $foo, $class))->isValidType(new Foo));
        $this->assertTrue((new DocblockFieldValidator($foo->getDocComment(), $foo, $class))->isValidType(new FooChild));

        $this->assertFalse((new DocblockFieldValidator($string->getDocComment(), $string, $class))->isValidType(1));
        $this->assertFalse((new DocblockFieldValidator($foo->getDocComment(), $foo, $class))->isValidType(new Bar));
    }

    /** @test */
    public function self_and_static_types_are_expanded()
    {
        [$class, $self, $static, $selfArray, $staticArray] = $this->getClassAndProperties(new FooChild);

        $this->assertEquals([Foo::class], (new DocblockFieldValidator($self->getDocComment(), $self, $class))->allowedTypes);
        $this->assertTrue((new DocblockFieldValidator($self->getDocComment(), $self, $class))->isValidType(new FooChild));
        $this->assertTrue((new DocblockFieldValidator($self->getDocComment(), $self, $class))->isValidType(new Foo));
        $this->assertFalse((new DocblockFieldValidator($self->getDocComment(), $self, $class))->isValidType(new Bar));

        $this->assertEquals([FooChild::class], (new DocblockFieldValidator($static->getDocComment(), $static, $class))->allowedTypes);
        $this->assertTrue((new DocblockFieldValidator($static->getDocComment(), $static, $class))->isValidType(new FooChild));
        $this->assertFalse((new DocblockFieldValidator($static->getDocComment(), $static, $class))->isValidType(new Foo));
        $this->assertFalse((new DocblockFieldValidator($static->getDocComment(), $static, $class))->isValidType(new Bar));

        $this->assertEquals([Foo::class], (new DocblockFieldValidator($selfArray->getDocComment(), $static, $class))->allowedArrayTypes);
        $this->assertTrue((new DocblockFieldValidator($selfArray->getDocComment(), $selfArray, $class))->isValidType([new FooChild]));
        $this->assertTrue((new DocblockFieldValidator($selfArray->getDocComment(), $selfArray, $class))->isValidType([new Foo]));
        $this->assertFalse((new DocblockFieldValidator($selfArray->getDocComment(), $selfArray, $class))->isValidType([new Bar]));

        $this->assertEquals([FooChild::class], (new DocblockFieldValidator($staticArray->getDocComment(), $static, $class))->allowedArrayTypes);
        $this->assertTrue((new DocblockFieldValidator($staticArray->getDocComment(), $staticArray, $class))->isValidType([new FooChild]));
        $this->assertFalse((new DocblockFieldValidator($staticArray->getDocComment(), $staticArray, $class))->isValidType([new Foo]));
        $this->assertFalse((new DocblockFieldValidator($staticArray->getDocComment(), $staticArray, $class))->isValidType([new Bar]));
    }

    private function getClassAndProperties(object $class): array
    {
        $reflectionClass = new ReflectionClass($class);

        return [$reflectionClass, ...$reflectionClass->getProperties()];
    }
}
