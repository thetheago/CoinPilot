<?php

declare(strict_types=1);

namespace App\ValueObjects;

use ArrayIterator;
use App\Models\Event;
use IteratorAggregate;

class Events implements IteratorAggregate
{
    /**
     * @var Event[]
     */
    private array $events;

    public function __construct()
    {
        $this->events = [];
    }

    public function addEvent(Event $event)
    {
        $this->events[] = $event;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->events);
    }
}
