<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class SuccessResponse extends AbstractSuccessResponse
{
    public static function make(array $data, ?int $code = null): JsonResponse
    {
        return static::makeResponse($data, $code);
    }
} 