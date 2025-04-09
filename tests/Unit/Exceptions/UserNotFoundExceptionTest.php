<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\UserNotFoundException;
use PHPUnit\Framework\TestCase;

class UserNotFoundExceptionTest extends TestCase
{
    public function testShouldCreateExceptionWithDefaultMessage(): void
    {
        $exception = new UserNotFoundException();

        $this->assertEquals(
            'Usuário não encontrado',
            $exception->getMessage()
        );
    }
}
