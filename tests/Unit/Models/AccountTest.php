<?php

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\Event;
use App\ValueObjects\Events;
use Tests\TestCase;

class AccountTest extends TestCase
{
    public function testDepositEventApply(): void
    {
        $account = new Account();
        $initialBalance = fake()->numberBetween(100, 999999);
        $account->balance = $initialBalance;
        $balanceToDeposit = fake()->numberBetween(100, 999999);
        
        $events = new Events();
        $event = new Event([
            'type' => 'Deposit',
            'payload' => json_encode(['balance' => $balanceToDeposit]),
            'version' => 1
        ]);
        $events->addEvent($event);

        $account->applyEach($events);
        $this->assertEquals($account->balance, $initialBalance + $balanceToDeposit);
    }

    public function testWithdrawEventApply(): void
    {
        $account = new Account();
        $initialBalance = fake()->numberBetween(100, 999999);
        $account->balance = $initialBalance;
        $balanceToWithdraw = fake()->numberBetween(100, 999999);
        
        $events = new Events();
        $event = new Event([
            'type' => 'Withdraw',
            'payload' => json_encode(['balance' => $balanceToWithdraw]),
            'version' => 1
        ]);
        $events->addEvent($event);

        $account->applyEach($events);

        $this->assertEquals($account->balance, $initialBalance - $balanceToWithdraw);
    }

    public function testDepositAndWithdrawEventApply(): void
    {
        $account = new Account();
        $initialBalance = 0;
        $account->balance = $initialBalance;
        $balanceToDeposit = 2;
        $balanceToWithdraw = 1;
        $balanceToDeposit2 = 1;
        
        $events = new Events();
        
        $depositEvent1 = new Event([
            'type' => 'Deposit',
            'payload' => json_encode(['balance' => $balanceToDeposit]),
            'version' => 1
        ]);
        $withdrawEvent = new Event([
            'type' => 'Withdraw',
            'payload' => json_encode(['balance' => $balanceToWithdraw]),
            'version' => 1
        ]);
        $depositEvent2 = new Event([
            'type' => 'Deposit',
            'payload' => json_encode(['balance' => $balanceToDeposit2]),
            'version' => 1
        ]);
        
        $events->addEvent($depositEvent1);
        $events->addEvent($withdrawEvent);
        $events->addEvent($depositEvent2);

        $account->applyEach($events);

        $this->assertEquals(
            $account->balance,
            $initialBalance + $balanceToDeposit - $balanceToWithdraw + $balanceToDeposit2
        );
    }
}
