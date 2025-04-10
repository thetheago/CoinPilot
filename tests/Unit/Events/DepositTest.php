<?php

namespace Tests\Unit\Events;

use Tests\TestCase;
use App\Events\Deposit;

class DepositTest extends TestCase
{
    public function testCanCreateDepositEvent()
    {
        // Arrange
        $payload = [
            'amount' => 100,
            'account_id' => 1
        ];

        // Act
        $event = new Deposit($payload);

        // Assert
        $this->assertInstanceOf(Deposit::class, $event);
        $this->assertEquals($payload, $event->payload);
    }
}
