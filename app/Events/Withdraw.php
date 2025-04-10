<?php

namespace App\Events;

use App\Interface\IEvent;

class Withdraw implements IEvent
{
    public function __construct(
        public string $payload
    ) {
    }
}
