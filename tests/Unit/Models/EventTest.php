<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function testEventHasCorrectFillableAttributes(): void
    {
        $fillable = ['type', 'payload', 'version', 'account_id'];
        $event = new Event();

        $this->assertEquals($fillable, $event->getFillable());
    }

    public function testEventBelongsToAccount(): void
    {
        $account = Account::factory()->create();
        $event = Event::factory()->create([
            'type' => 'deposit',
            'payload' => json_encode(['account_payer' => 3, 'account_payee' => $account->id, 'balance' => 3021]),
            'version' => 0,
            'account_id' => $account->id,
        ]);

        $this->assertInstanceOf(Account::class, $event->account);
        $this->assertEquals($account->id, $event->account->id);
    }

    public function testEventFactoryCreatesValidEvent(): void
    {
        $account = Account::factory()->create();
        $event = Event::factory()->create([
            'type' => 'deposit',
            'payload' => json_encode([
                'account_payer' => 3,
                'account_payee' => $account->id,
                'balance' => 3021
            ]),
            'version' => 0,
            'account_id' => $account->id,
        ]);

        $this->assertInstanceOf(Event::class, $event);
        $this->assertNotNull($event->type);
        $this->assertNotNull($event->payload);
        $this->assertNotNull($event->version);
        $this->assertNotNull($event->account_id);
    }
} 