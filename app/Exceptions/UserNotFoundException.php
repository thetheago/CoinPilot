<?php

declare(strict_types=1);

namespace App\Exceptions;

class UserNotFoundException extends \DomainException
{
    public function __construct(string $message = 'Usuário não encontrado', int $code = 404)
    {
        parent::__construct($message, $code);
    }
}
