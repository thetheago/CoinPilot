<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct(string $message = 'Usuário não encontrado', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}