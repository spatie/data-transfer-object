<?php

namespace Spatie\ValueObject\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function markTestSucceeded()
    {
        $this->assertTrue(true);
    }
}