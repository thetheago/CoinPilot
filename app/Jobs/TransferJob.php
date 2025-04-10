<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TransferJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $payer,
        public User $payee,
        public int $balance,
    ) {
        $this->onQueue('transfer');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('TransferJob');
    }
}
