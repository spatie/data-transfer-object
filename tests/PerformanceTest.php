<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class MyDTO extends DataTransferObject {
    /** @var int */
    public $i;
}

class PerformanceTest extends TestCase
{
    /** @test */
    public function test()
    {
        foreach (range(1, 500000) as $i) {
            new MyDTO([
                'i' => $i,
            ]);
        }

        $this->markTestSucceeded();
    }
}
