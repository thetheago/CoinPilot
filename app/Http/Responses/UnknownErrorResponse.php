<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UnknownErrorResponse extends AbstractErrorResponse
{
    protected const DEFAULT_STATUS_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;

    public static function make(string $message): JsonResponse
    {
        return static::makeResponse([$message]);
    }
}
