<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use App\Exceptions\NotEnoughCashException;
use Tests\TestCase;

class NotEnoughCashExceptionTest extends TestCase
{
    public function testShouldCreateExceptionWithDefaultMessage(): void
    {
        $exception = new NotEnoughCashException();
        
        $this->assertEquals(
            'Saldo insuficiente para a transferência.',
            $exception->getMessage()
        );
    }

    public function testShouldCreateExceptionWithCustomMessage(): void
    {
        $customMessage = 'Saldo insuficiente para a transferência.';
        $exception = new NotEnoughCashException($customMessage);
        
        $this->assertEquals($customMessage, $exception->getMessage());
    }
}
