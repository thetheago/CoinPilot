<?php

declare(strict_types=1);

namespace App\Exceptions;

class LojistAsAPayerException extends \DomainException
{
    public function __construct(string $message = 'O payer é um lojista, lojistas não podem realizar transações.')
    {
        parent::__construct($message);
    }
}
