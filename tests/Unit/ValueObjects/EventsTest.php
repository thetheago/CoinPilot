<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Events;
use App\Models\Event;
use Tests\TestCase;

class EventsTest extends TestCase
{
    public function testShouldCreateEvents(): void
    {
        $events = new Events();
        $event = new Event();
        $event2 = new Event();
        
        $events->addEvent($event);
        $events->addEvent($event2);
        
        $this->assertCount(2, $events->getIterator());
        $this->assertInstanceOf(Event::class, $events->getIterator()->current());
    }
}
