<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\TransferJob;
use App\Models\User;
use App\Models\Account;
use App\Interface\IEventsRepository;
use App\Exceptions\NotEnoughCashException;
use App\Repositories\EventsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Event;

class TransferJobTest extends TestCase
{
    use RefreshDatabase;

    private IEventsRepository $eventsRepository;
    private User $payer;
    private User $payee;
    private Account $payerAccount;
    private Account $payeeAccount;

    private int $initialPayerBalance = 100050;
    private int $initialPayeeBalance = 100050;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payer = User::factory()->create();
        $this->payee = User::factory()->create();

        $this->payerAccount = Account::factory()->create([
            'balance' => $this->initialPayerBalance,
            'user_id' => $this->payer->id
        ]);
        $this->payeeAccount = Account::factory()->create([
            'balance' => $this->initialPayeeBalance,
            'user_id' => $this->payee->id
        ]);

        $this->payer->account_id = $this->payerAccount->id;
        $this->payer->save();

        $this->payee->account_id = $this->payeeAccount->id;
        $this->payee->save();
        
        $this->eventsRepository = new EventsRepository();
    }

    // public function testTransferSuccessful()
    // {
    //     $balance = 40000;

    //     // Criar eventos iniciais para simular o estado das contas
    //     $payerEvents = new Events();
    //     $payerEvents->addEvent(new Event([
    //         'type' => 'Deposit',
    //         'payload' => json_encode(['balance' => $this->initialPayerBalance]),
    //         'version' => 1
    //     ]));

    //     $payeeEvents = new Events();
    //     $payeeEvents->addEvent(new Event([
    //         'type' => 'Deposit',
    //         'payload' => json_encode(['balance' => $this->initialPayeeBalance]),
    //         'version' => 1
    //     ]));

    //     $eventsRepositoryMock = Mockery::mock(IEventsRepository::class);
    //     $eventsRepositoryMock->shouldReceive('getEventsOfAgregate')
    //         ->with(Mockery::type(Account::class))
    //         ->andReturnUsing(function ($account) use ($payerEvents, $payeeEvents) {
    //             return $account->id === $this->payerAccount->id ? $payerEvents : $payeeEvents;
    //         });
    //     $eventsRepositoryMock->shouldReceive('persistAgreggateEvents')
    //         ->with(Mockery::type(Account::class))
    //         ->andReturnNull();

    //     $job = new TransferJob(
    //         $this->payer,
    //         $this->payee,
    //         $balance,
    //         $eventsRepositoryMock
    //     );
        
    //     $job->handle();

        
    //     $withdrawEvent = $payerEvents->first(function ($event) {
    //         return $event->type === 'Withdraw' && $event->account_id === $this->payerAccount->id;
    //     });
    //     $this->assertNotNull($withdrawEvent);
    //     $this->assertEquals(json_encode(['balance' => $balance]), $withdrawEvent->payload);

    //     $depositEvent = $payeeEvents->first(function ($event) {
    //         return $event->type === 'Deposit' && $event->account_id === $this->payeeAccount->id;
    //     });
    //     $this->assertNotNull($depositEvent);
    //     $this->assertEquals(
    //         json_encode([
    //             'account_payer' => $this->payerAccount->id,
    //             'account_payee' => $this->payeeAccount->id,
    //             'balance' => $balance
    //         ]),
    //         $depositEvent->payload
    //     );
    // }

    public function testTransferFailsWhenNotEnoughBalance()
    {
        $payer = User::factory()->create();
        $payerAccount = Account::factory()->create(['balance' => 20000, 'user_id' => $payer->id]);

        $payer->account_id = $payerAccount->id;
        $payer->save();
        
        
        $this->expectException(NotEnoughCashException::class);
        
        $job = new TransferJob(
            $payer,
            $this->payee,
            30000,
            $this->eventsRepository
        );

        $job->handle();
    }
}
