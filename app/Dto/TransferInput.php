<?php

declare(strict_types=1);

namespace App\Dto;

class TransferInput
{
    public function __construct(
        public int $payer,
        public int $payee,
        public float $value
    ) {
    }

    public function getPayer(): int
    {
        return $this->payer;
    }

    public function getPayee(): int
    {
        return $this->payee;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
