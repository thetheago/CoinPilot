<?php

namespace App\Exceptions;

use Exception;

class ConcurrencyException extends Exception
{
    public function __construct(string $message = 'Problema de concorrência.')
    {
        parent::__construct($message);
    }
}
