<?php

declare(strict_types=1);

namespace App\Dto;

class TransferInput
{
    public function __construct(
        public int $payer,
        public int $payee,
        public float $value) {}
}