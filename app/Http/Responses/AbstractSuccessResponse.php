<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractSuccessResponse
{
    protected const DEFAULT_STATUS_CODE = Response::HTTP_OK;

    protected static function makeResponse(array $data, ?int $code = null): JsonResponse
    {
        return response()->json($data, $code ?? static::DEFAULT_STATUS_CODE);
    }
}
