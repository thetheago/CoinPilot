<?php

declare(strict_types=1);

namespace App\Services;

use App\Interface\INotificationService;
use Illuminate\Support\Facades\Http;

class MailNotificationService implements INotificationService
{
    public function sendNotification(string $message): void
    {
        Http::post('https://util.devi.tools/api/v1/notify', [
            'message' => $message,
        ]);
    }
}
