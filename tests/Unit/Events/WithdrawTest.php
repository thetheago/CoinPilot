<?php

namespace Tests\Unit\Events;

use Tests\TestCase;
use App\Events\Withdraw;

class WithdrawTest extends TestCase
{
    public function testCanCreateWithdrawEvent()
    {
        // Arrange
        $payload = [
            'amount' => 100,
            'account_id' => 1
        ];

        // Act
        $event = new Withdraw($payload);

        // Assert
        $this->assertInstanceOf(Withdraw::class, $event);
        $this->assertEquals($payload, $event->payload);
    }
}
