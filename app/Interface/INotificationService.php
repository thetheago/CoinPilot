<?php

namespace App\Interface;

interface INotificationService
{
    public function sendNotification(string $message): void;
}
