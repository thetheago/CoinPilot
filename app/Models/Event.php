<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'payload',
        'version',
        'account_id'
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
