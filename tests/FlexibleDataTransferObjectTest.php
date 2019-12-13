<?php

namespace Spatie\DataTransferObject\Tests;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class FlexibleDataTransferObjectTest extends TestCase
{
    /** @test */
    public function flexible_dto_can_be_given_unknown_properties()
    {
        $dto = new class(['unknown' => 'test']) extends FlexibleDataTransferObject {
        };

        $this->markTestSucceeded();
    }
}
