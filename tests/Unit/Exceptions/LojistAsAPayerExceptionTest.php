<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\LojistAsAPayerException;
use PHPUnit\Framework\TestCase;

class LojistAsAPayerExceptionTest extends TestCase
{
    public function testShouldCreateExceptionWithDefaultMessage(): void
    {
        $exception = new LojistAsAPayerException();
        
        $this->assertEquals(
            'O payer é um lojista, lojistas não podem realizar transações.',
            $exception->getMessage()
        );
    }

    public function testShouldCreateExceptionWithCustomMessage(): void
    {
        $customMessage = 'Lojista não realiza transações.';
        $exception = new LojistAsAPayerException($customMessage);
        
        $this->assertEquals($customMessage, $exception->getMessage());
    }
}
