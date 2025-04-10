<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\MailNotificationService;
use Tests\TestCase;

class MailNotificationServiceTest extends TestCase
{
    public function testSendNotification(): void
    {
        $mailNotificationService = new MailNotificationService();
        $mailNotificationService->sendNotification('msg');

        $this->assertTrue(true);
    }
}
