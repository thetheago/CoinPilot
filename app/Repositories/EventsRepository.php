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
        $eventsCollection = Event::where('account_id', $agregate->id)->get();

        $events = new Events();

        foreach ($eventsCollection->toArray() as $event) {
            $events->addEvent($event);
        }

        return $events;
    }
}
