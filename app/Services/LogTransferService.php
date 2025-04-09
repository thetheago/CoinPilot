<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

class LogTransferService
{
    public static function info(string $message, array $context = []): void
    {
        Log::channel('transfer')->info($message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        Log::channel('transfer')->debug($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        Log::channel('transfer')->error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        Log::channel('transfer')->warning($message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        Log::channel('transfer')->critical($message, $context);
    }
}
