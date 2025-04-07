<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

abstract class AbstractSuccessResponse
{
    protected const DEFAULT_STATUS_CODE = 200;

    protected static function makeResponse(array $data, ?int $code = null): JsonResponse
    {
        return response()->json($data, $code ?? static::DEFAULT_STATUS_CODE);
    }
} 