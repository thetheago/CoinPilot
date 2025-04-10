<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\User;
use App\Models\Account;
use App\Interface\IEventsRepository;
use Exception;
use App\Services\LogTransferService;
use App\Exceptions\ConcurrencyException;
class RefundJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $payer,
        public int $balance,
        public IEventsRepository $eventsRepository
    ) {
        $this->onQueue('transfer');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        while (true) {
            try {
                /**
                 * @var Account $payerAccount
                 */
                $payerAccount = $this->payer->account;

                $payerAccount->balance = 0;

                $events = $this->eventsRepository->getEventsOfAgregate($payerAccount);
                $payerAccount->applyEach($events);

                $payerAccount->refund(balance: $this->balance);

                $this->eventsRepository->persistAgreggateEvents($payerAccount);

                // TODO: Enviar notificação para o payer sobre o reembolso
                return;
            } catch (Exception $e) {
                LogTransferService::critical($e->getMessage(), [$e->getTraceAsString()]);
                throw $e;
            } catch (ConcurrencyException $e) {
                LogTransferService::warning($e->getMessage(), [$e->getTraceAsString()]);
            }
        }
    }
}
