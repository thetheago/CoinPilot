<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

abstract class AbstractErrorResponse
{
    protected const DEFAULT_STATUS_CODE = 400;

    protected static function makeResponse(array $errors, ?int $code = null): JsonResponse
    {
        return response()->json(['errors' => $errors], $code ?? static::DEFAULT_STATUS_CODE);
    }
} 