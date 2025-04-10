<?php

declare(strict_types=1);

namespace App\Interface;

use App\Models\Account;
use App\ValueObjects\Events;

interface IEventsRepository
{
    public function getEventsOfAgregate(Account $agregate): Events;
}
