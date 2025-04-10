<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Interface\IESAgregate;
use App\Interface\IEvent;

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
}
