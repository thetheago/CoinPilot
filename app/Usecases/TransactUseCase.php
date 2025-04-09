<?php

declare(strict_types=1);

namespace App\Usecases;

use App\Dto\TransferInput;
use App\Interface\IUserRepository;
use App\Exceptions\LojistAsAPayerException;
use App\Exceptions\UserNotFoundException;
use App\Interface\IAuthorizeService;
use App\Exceptions\UnauthorizedException;

class TransactUseCase
{
    public function __construct(
        private readonly IUserRepository $userRepository,
        private readonly IAuthorizeService $authorizeService,
    ) {
    }

    /**
     * @throws LojistAsAPayerException
     * @throws \DomainException
     */
    public function execute(TransferInput $input): void
    {
        try {
            $payer = $this->userRepository->getUserById($input->getPayer());
        } catch (UserNotFoundException $e) {
            throw new \DomainException("Payer {$input->getPayer()} não encontrado.");
        }

        try {
            $payee = $this->userRepository->getUserById($input->getPayee());
    
            if ($payer->isLojista()) {
                throw new LojistAsAPayerException();
            }
    
            // Checando da projeção
            if ($payer->getBalance() < $input->getValue()) {
                throw new \DomainException('O payer não tem saldo suficiente para realizar a transação.');
            }
    
            $isAuthorized = $this->authorizeService->checkAuthorization();
            if (!$isAuthorized) {
                throw new UnauthorizedException();
            }

            // TODO: Manda para a fila
        } catch (UserNotFoundException $e) {
            throw new \DomainException("Payee {$input->getPayee()} não encontrado.");
        }
    }
}
