<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\AuthorizeService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthorizeServiceTest extends TestCase
{
    private AuthorizeService $authorizeService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authorizeService = new AuthorizeService();
    }

    public function testCheckAuthorizationReturnsTrueWhenAuthorized(): void
    {
        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response([
                'status' => 'success'
            ], 200)
        ]);

        $result = $this->authorizeService->checkAuthorization();

        $this->assertTrue($result);
    }

    public function testCheckAuthorizationReturnsFalseWhenNotAuthorized(): void
    {
        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response([
                'status' => 'fail'
            ], 200)
        ]);

        $result = $this->authorizeService->checkAuthorization();

        $this->assertFalse($result);
    }
}
