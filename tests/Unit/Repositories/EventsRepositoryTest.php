<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Account;
use App\Models\Event;
use App\Repositories\EventsRepository;
use App\ValueObjects\Events;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldGetEventsOfAgregate(): void
    {
        $account = Account::factory()->create();
        $event = Event::factory()->create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'payload' => json_encode(['account_payer' => 3, 'account_payee' => $account->id, 'balance' => 3021]),
            'version' => 0,
        ]);

        $repository = new EventsRepository();
        $events = $repository->getEventsOfAgregate($account);

        $this->assertInstanceOf(Events::class, $events);
        $this->assertCount(1, $events->getIterator());
        $this->assertEquals($event->id, $events->getIterator()->current()->id);
    }

    public function testEventsShouldBeOrderedByVersion(): void
    {
        $firstVersion = 0;
        $secondVersion = 1;
        $thirdVersion = 2;

        $account = Account::factory()->create();
        Event::factory()->create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'payload' => json_encode(['account_payer' => 3, 'account_payee' => $account->id, 'balance' => 3021]),
            'version' => $firstVersion,
        ]);

        Event::factory()->create([
            'account_id' => $account->id,
            'type' => 'withdraw',
            'payload' => json_encode(['balance' => 120]),
            'version' => $secondVersion,
        ]);

        Event::factory()->create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'payload' => json_encode(['account_payer' => 3, 'account_payee' => $account->id, 'balance' => 9811]),
            'version' => $thirdVersion,
        ]);

        $repository = new EventsRepository();
        $events = $repository->getEventsOfAgregate($account);

        $i = 0;

        foreach ($events as $event) {
            $this->assertEquals($i, $event->version);
            $i++;
        }
    }
}
