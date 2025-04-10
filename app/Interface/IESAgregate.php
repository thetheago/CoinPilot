<?php

declare(strict_types=1);

namespace App\Interface;

use App\ValueObjects\Events;

interface IESAgregate
{
    public function applyEach(Events $events): void;
}
