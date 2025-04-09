<?php

declare(strict_types=1);

namespace App\Services;

use App\Interface\IAuthorizeService;
use Illuminate\Support\Facades\Http;

class AuthorizeService implements IAuthorizeService
{
    public function checkAuthorization(): bool
    {
        $response = Http::get('https://util.devi.tools/api/v2/authorize');
        $data = $response->json();

        if ($data['status'] != 'success') {
            return false;
        }

        return true;
    }
}
