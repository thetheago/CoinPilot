<?php

declare(strict_types=1);

namespace App\Events;

use App\Interface\IEvent;

class Refund implements IEvent
{
    public function __construct(
        public array $payload
    ) {
    }
}
