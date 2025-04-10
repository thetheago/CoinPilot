<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Account;
use App\Models\Event;
use App\ValueObjects\Events;
use App\Interface\IEventsRepository;

class EventsRepository implements IEventsRepository
{
    public function getEventsOfAgregate(Account $agregate): Events
    {
        $events = Event::where('account_id', $agregate->id)->get();

        // TODO: Verificar se funciona desse jeito.
        return new Events($events);
    }
}
