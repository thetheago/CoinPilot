<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\Account;
use App\Models\Event;
use App\Repositories\EventsRepository;
use App\ValueObjects\Events;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use App\Events\Deposit;
use App\Exceptions\ConcurrencyException;

class EventsRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldGetEventsOfAgregate(): void
    {
        $account = Account::factory()->create();
        $event = Event::factory()->create([
            'account_id' => $account->id,
            'type' => 'Deposit',
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
            'type' => 'Deposit',
            'payload' => json_encode(['account_payer' => 3, 'account_payee' => $account->id, 'balance' => 3021]),
            'version' => $firstVersion,
        ]);

        Event::factory()->create([
            'account_id' => $account->id,
            'type' => 'Withdraw',
            'payload' => json_encode(['balance' => 120]),
            'version' => $secondVersion,
        ]);

        Event::factory()->create([
            'account_id' => $account->id,
            'type' => 'Deposit',
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

    public function testPersistAgreggateEventsMustReturnIfAgregateHasNoEvents(): void
    {
        $account = Account::factory()->create();
        $repository = new EventsRepository();
        $repository->persistAgreggateEvents($account);

        $this->assertDatabaseCount('events', 0);
    }

    public function testOptimisticLock(): void
    {
        $account = Account::factory()->create();

        Event::factory()->create([
            'account_id' => $account->id,
            'type' => 'Deposit',
            'payload' => json_encode(['account_payer' => 3, 'account_payee' => $account->id, 'balance' => 3021]),
            'version' => 3,
        ]);

        $accountMock = Mockery::mock(Account::class);
        $accountMock->shouldReceive('getAttribute')->with('id')->andReturn($account->id);

        $eventsMock = Mockery::mock(Events::class);
        $eventsMock->shouldReceive('getIterator')->andReturn([Mockery::mock(Event::class)]);

        $accountMock->shouldReceive('getPendingEvents')->andReturn([new Deposit(
            ['account_payer' => 3,
            'account_payee' => $account->id,
            'balance' => 3021]
            )
        ]);
        $accountMock->shouldReceive('getAttribute')->with('versionOfLastEvent')->andReturn(1);

        $repository = new EventsRepository();
        $this->expectException(ConcurrencyException::class);
        $this->expectExceptionMessage('Conflito de concorrência.
                    Versão esperada: 0, Versão do banco: 3');
        $repository->persistAgreggateEvents($accountMock);
    }
}
