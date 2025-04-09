<?php

declare(strict_types=1);

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UserNotFoundException extends \DomainException
{
    public function __construct(string $message = 'Usuário não encontrado', int $code = Response::HTTP_NOT_FOUND)
    {
        parent::__construct($message, $code);
    }
}
