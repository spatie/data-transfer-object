<?php

namespace Spatie\DataTransferObject\Tests\Reflection;

use Spatie\DataTransferObject\Reflection\DataTransferObjectClass;
use Spatie\DataTransferObject\Tests\AnonymousDataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;

class DataTransferObjectClassTest extends TestCase
{
    /** @test */
    public function test_public_properties()
    {
        $dto = new class() extends AnonymousDataTransferObject {
            public $foo;

            public static $bar;

            private $baz;

            protected $boo;
        };

        $class = new DataTransferObjectClass($dto);

        $this->assertCount(1, $class->getProperties());
        $this->assertEquals('foo', $class->getProperties()[0]->name);
    }
}
