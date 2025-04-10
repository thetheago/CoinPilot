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
        try {
            /**
             * @var Account $payerAccount
             */
            $payerAccount = $this->payer->account;

            /**
             * @var Account $payeeAccount
             */
            $payeeAccount = $this->payee->account;

            $events = $this->eventsRepository->getEventsOfAgregate($payerAccount);
            $payerAccount->applyEach($events);

            if ($payerAccount->getBalance() < $this->balance) {
                throw new NotEnoughCashException();
            }

            // $payerAccount->withdraw($this->balance);

            // TODO: Se alguma falha ocorrer aqui, entre o withdraw e o deposit deve gerar um job de refund.

            // $payeeAccount->deposit($this->balance);

            // TODO: Atualiza projeções.
            // TODO: Enviar notificação para o payer
        } catch (NotEnoughCashException $e) {
            // TODO: Enviar notificação para o payer
        } catch (Exception $e) {
            LogTransferService::critical($e->getMessage(), [$e->getTraceAsString()]);
        }
    }
}
