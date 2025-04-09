<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Responses;

use App\Http\Responses\UnknownErrorResponse;
use Tests\TestCase;

class UnknownErrorResponseTest extends TestCase
{
    public function testAssertUnknownErrorResponse(): void
    {
        $message = 'Algo de errado aconteceu.';
        $response = UnknownErrorResponse::make($message);
        
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            ['errors' => [$message]],
            $response->getData(true)
        );
    }
}
