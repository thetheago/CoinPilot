<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interface\IESAgregate;
use App\Interface\IEvent;
use App\ValueObjects\Events;

abstract class AbstractESAgreggate extends Model implements IESAgregate
{
    /**
     * @var array<IEvent>
     */
    protected $pendingEvents = [];

    public function recordEvent(IEvent $event): void
    {
        $this->pendingEvents[] = $event;
    }

    public function getPendingEvents(): array
    {
        return $this->pendingEvents;
    }

    public function applyEach(Events $events): void
    {
        if (count($events->getIterator()) === 0) {
            return;
        }

        foreach ($events as $event) {
            $method = 'apply' . $event->type . 'Event';
            $this->{$method}($event->payload);

            $this->versionOfLastEvent = $event->version;
        }
    }
}
