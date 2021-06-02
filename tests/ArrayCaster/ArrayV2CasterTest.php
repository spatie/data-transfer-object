<?php

namespace Spatie\DataTransferObject\Tests\ArrayCaster;

use Spatie\DataTransferObject\DataTransferObject;
use Spatie\DataTransferObject\Tests\TestCase;

class ArrayV2CasterTest extends TestCase
{
    /** @test */
    public function version_2_will_casted()
    {
        $bar = new ParentDto(
            [
                'order_confirmation' => [
                    'admin' => [
                        'email' => 'dummy-1@gmail.com',
                    ],
                ],
            ]
        );

        $this->assertEquals('dummy-1@gmail.com',$bar->order_confirmation->admin->email);
    }
}

class ParentDto extends DataTransferObject
{
    public OrderConfirmation $order_confirmation;

    public function __construct(array $parameters = [])
    {
        if (! isset($parameters['order_confirmation'])) {
            $parameters['order_confirmation'] = new OrderConfirmation();
        }

        parent::__construct($parameters);
    }
}

class OrderConfirmation extends DataTransferObject
{
    public Confirmation $admin;

    public function __construct(array $parameters = [])
    {
        if (! isset($parameters['admin'])) {
            $parameters['admin'] = new Confirmation();
        }

        parent::__construct($parameters);
    }
}

class Confirmation extends DataTransferObject
{
    public ?string $email = null;
}

