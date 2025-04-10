<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\User;
use App\Exceptions\NotEnoughCashException;
use Exception;
use App\Services\LogTransferService;
use App\Models\Account;
use App\Interface\IEventsRepository;

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
        public IEventsRepository $eventsRepository
    ) {
        $this->onQueue('transfer');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $withdrawWasPersisted = false;

        try {
            /**
             * @var Account $payerAccount
             */
            $payerAccount = $this->payer->account;

            /**
             * @var Account $payeeAccount
             */
            $payeeAccount = $this->payee->account;

            $payerAccount->balance = 0;
            $payeeAccount->balance = 0;

            $events = $this->eventsRepository->getEventsOfAgregate($payerAccount);
            $payerAccount->applyEach($events);

            if ($payerAccount->balance < $this->balance) {
                throw new NotEnoughCashException();
            }

            $payerAccount->withdraw($this->balance);

            $this->eventsRepository->persistAgreggateEvents($payerAccount);

            $withdrawWasPersisted = true;

            $payeeAccount->deposit(
                balance: $this->balance,
                idAccountPayer: $payerAccount->id,
                idAccountPayee: $payeeAccount->id
            );

            $events = $this->eventsRepository->getEventsOfAgregate($payeeAccount);
            $payeeAccount->applyEach($events);
            $this->eventsRepository->persistAgreggateEvents($payeeAccount);

            // TODO: Enviar notificação para o payer
        } catch (NotEnoughCashException $e) {
            // TODO: Enviar notificação para o payer
            throw $e;
        } catch (Exception $e) {
            if ($withdrawWasPersisted) {
                RefundJob::dispatch(
                    payer: $this->payer,
                    balance: $this->balance,
                    eventsRepository: $this->eventsRepository
                );
            }

            LogTransferService::critical($e->getMessage(), [$e->getTraceAsString()]);
        }
    }
}
