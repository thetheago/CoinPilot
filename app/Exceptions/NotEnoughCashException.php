<?php

namespace App\Exceptions;

class NotEnoughCashException extends \DomainException
{
    public function __construct()
    {
        parent::__construct('Saldo insuficiente para a transferência.');
    }
}
