<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Interface\IESAgregate;
use App\ValueObjects\Events;
use BcMath\Number;

class Account extends Model implements IESAgregate
{
    use HasFactory;

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
        foreach ($events as $event) {
            $method = $event->type . 'EventApply';
            $this->{$method}($event->payload);
        }
    }

    public function depositEventApply(string $payload): self
    {
        $payload = json_decode($payload, true);

        $balance = new Number($this->balance);
        $balanceToAdd = new Number($payload['balance']);

        $sum = $balance->add($balanceToAdd);

        $this->balance = (int) $sum->value;

        return $this;
    }

    public function withdrawEventApply(string $payload): self
    {
        $payload = json_decode($payload, true);

        $balance = new Number($this->balance);
        $balanceToSubtract = new Number($payload['balance']);

        $sub = $balance->sub($balanceToSubtract);

        $this->balance = (int) $sub->value;

        return $this;
    }
}
