<?php

namespace App\Events;

use App\Interface\IEvent;

class Deposit implements IEvent
{
    public function __construct(
        public array $payload
    ) {
    }
}
