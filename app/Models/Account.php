<?php

namespace App\Models;

use App\Events\Withdraw;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\ValueObjects\Events;
use BcMath\Number;
use App\Events\Deposit;
use App\Events\Refund;

class Account extends AbstractESAgreggate
{
    use HasFactory;

    public int $versionOfLastEvent = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * Get the user that owns the account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function applyDepositEvent(string $payload): self
    {
        $payload = json_decode($payload, true);

        $balance = new Number($this->balance);
        $balanceToAdd = new Number($payload['balance']);

        $sum = $balance->add($balanceToAdd);

        $this->balance = (int) $sum->value;

        return $this;
    }

    public function applyWithdrawEvent(string $payload): self
    {
        $payload = json_decode($payload, true);

        $balance = new Number($this->balance);
        $balanceToSubtract = new Number($payload['balance']);

        $sub = $balance->sub($balanceToSubtract);

        $this->balance = (int) $sub->value;

        return $this;
    }

    public function applyRefundEvent(string $payload): self
    {
        $payload = json_decode($payload, true);

        $balance = new Number($this->balance);
        $balanceToAdd = new Number($payload['balance']);

        $sum = $balance->add($balanceToAdd);

        $this->balance = (int) $sum->value;

        return $this;
    }

    public function withdraw(int $balance): self
    {
        $this->recordEvent(new Withdraw(['balance' => $balance]));
        return $this;
    }

    public function deposit(int $balance, int $idAccountPayer, int $idAccountPayee): self
    {
        $this->recordEvent(new Deposit([
            'account_payer' => $idAccountPayer,
            'account_payee' => $idAccountPayee,
            'balance' => $balance,
        ]));

        return $this;
    }

    public function refund(int $balance): self
    {
        $this->recordEvent(new Refund(['balance' => $balance]));
        return $this;
    }
}
