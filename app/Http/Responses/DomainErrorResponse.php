<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DomainErrorResponse extends AbstractErrorResponse
{
    protected const DEFAULT_STATUS_CODE = Response::HTTP_UNPROCESSABLE_ENTITY;

    public static function make(string $message): JsonResponse
    {
        return static::makeResponse([$message]);
    }
}
