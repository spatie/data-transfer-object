<?php

namespace Spatie\DataTransferObject\Tests\Support;

use Spatie\DataTransferObject\Support\Str;
use Spatie\DataTransferObject\Tests\TestCase;

class DataTransferObjectClassTest extends TestCase
{
    /** @test */
    public function test_snake_case()
    {
        $this->assertEquals('hello_world', Str::snake("HelloWorld"));
        $this->assertEquals('hello_world', Str::snake("helloWorld"));
    }

    /** @test */
    public function test_camel_case()
    {
        $this->assertEquals('helloWorld', Str::camel("Hello_World"));
        $this->assertEquals('helloWorld', Str::camel("hello_world"));
        $this->assertEquals('helloWorld', Str::camel("Hello_world"));
        $this->assertEquals('helloWorld', Str::camel("hello_World"));
    }

    /** @test */
    public function test_studly_case()
    {
        $this->assertEquals('HelloWorld', Str::studly("Hello_World"));
        $this->assertEquals('HelloWorld', Str::studly("hello_world"));
        $this->assertEquals('HelloWorld', Str::studly("Hello_world"));
        $this->assertEquals('HelloWorld', Str::studly("hello_World"));
    }
}
