<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class UnknownErrorResponse extends AbstractErrorResponse
{
    protected const DEFAULT_STATUS_CODE = 500;

    public static function make(string $message): JsonResponse
    {
        return static::makeResponse([$message]);
    }
}
