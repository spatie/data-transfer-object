<?php

namespace Spatie\DataTransferObject\Tests\Casters;

use Spatie\DataTransferObject\Attributes\CastWith;
use Spatie\DataTransferObject\Casters\BooleanCaster;
use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;

class BooleanCasterTest extends TestCase
{
    /** @test */
    public function test_boolean_caster()
    {
        $this->assertTrue((new BooleanDTO(['active' => 'yes']))->active);
        $this->assertTrue((new BooleanDTO(['active' => 'on']))->active);
        $this->assertTrue((new BooleanDTO(['active' => '1']))->active);
        $this->assertTrue((new BooleanDTO(['active' => 1]))->active);
        $this->assertTrue((new BooleanDTO(['active' => true]))->active);
        $this->assertTrue((new BooleanDTO(['active' => 'true']))->active);

        $this->assertFalse((new BooleanDTO(['active' => 'no']))->active);
        $this->assertFalse((new BooleanDTO(['active' => 'off']))->active);
        $this->assertFalse((new BooleanDTO(['active' => '0']))->active);
        $this->assertFalse((new BooleanDTO(['active' => 0]))->active);
        $this->assertFalse((new BooleanDTO(['active' => false]))->active);
        $this->assertFalse((new BooleanDTO(['active' => 'false']))->active);

        $this->assertFalse((new BooleanDTO(['active' => 'unknown']))->active);
    }
}

class BooleanDTO extends DataTransferObject
{
    #[CastWith(BooleanCaster::class)]
    public bool $active;
}
