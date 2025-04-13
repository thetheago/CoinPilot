<?php

declare(strict_types=1);

namespace Tests\Unit\Dto;
use App\Dto\TransferInput;
use Tests\TestCase;

class TransferInputTest extends TestCase
{
    public function testTransferInput(): void
    {
        $payerId = fake()->numberBetween(1, 100);
        $payeeId = fake()->numberBetween(1, 100);
        $value = fake()->randomFloat(2, 0, 1000);

        $transferInput = new TransferInput(
            $payerId,
            $payeeId,
            $value
        );

        $this->assertEquals($payerId, $transferInput->getPayer());
        $this->assertEquals($payeeId, $transferInput->getPayee());
        $this->assertEquals($value, $transferInput->getValue());
    }
}
