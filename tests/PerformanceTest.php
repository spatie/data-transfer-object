<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\DataTransferObject;

class MyDTO extends DataTransferObject
{
    public int $i;
}

class PerformanceTest extends TestCase
{
    /** @test */
    public function test()
    {
        foreach (range(1, 5_000_00) as $i) {
            new MyDTO([
                'i' => $i,
            ]);
        }

        $this->markTestSucceeded();
    }
}
