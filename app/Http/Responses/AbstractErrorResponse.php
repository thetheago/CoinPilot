<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractErrorResponse
{
    protected const DEFAULT_STATUS_CODE = Response::HTTP_BAD_REQUEST;

    protected static function makeResponse(array $errors, ?int $code = null): JsonResponse
    {
        return response()->json(['errors' => $errors], $code ?? static::DEFAULT_STATUS_CODE);
    }
}
