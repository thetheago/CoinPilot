<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Responses;

use App\Http\Responses\DomainErrorResponse;
use Tests\TestCase;

class DomainErrorResponseTest extends TestCase
{
    public function testAssertDomainErrorResponse(): void
    {
        $message = 'Lojista não realiza transações.';
        $response = DomainErrorResponse::make($message);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(
            ['errors' => [$message]],
            $response->getData(true)
        );
    }
}
