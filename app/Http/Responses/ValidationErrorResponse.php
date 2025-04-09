<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class ValidationErrorResponse extends AbstractErrorResponse
{
    protected const DEFAULT_STATUS_CODE = Response::HTTP_UNPROCESSABLE_ENTITY;

    public static function make(Validator $validator, ?int $code = null): JsonResponse
    {
        return static::makeResponse($validator->errors()->all(), $code);
    }
}
