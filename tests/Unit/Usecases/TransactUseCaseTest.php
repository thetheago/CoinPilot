<?php

declare(strict_types=1);

namespace Tests\Unit\Usecases;

use App\Dto\TransferInput;
use App\Exceptions\LojistAsAPayerException;
use App\Interface\IUserRepository;
use App\Interface\IAuthorizeService;
use App\Models\User;
use App\Usecases\TransactUseCase;
use PHPUnit\Framework\TestCase;
use App\Exceptions\UnauthorizedException;
use Mockery;

class TransactUseCaseTest extends TestCase
{
    private IUserRepository $userRepository;
    private IAuthorizeService $authorizeService;
    private TransactUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = Mockery::mock(IUserRepository::class);
        $this->authorizeService = Mockery::mock(IAuthorizeService::class);
        $this->useCase = new TransactUseCase($this->userRepository, $this->authorizeService);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testShouldThrowExceptionWhenPayerIsLojista(): void
    {
        $this->expectException(LojistAsAPayerException::class);

        $payer = Mockery::mock(User::class);
        $payer->shouldReceive('isLojista')->andReturn(true);
        $payer->shouldReceive('getBalance')->andReturn(1000.00);

        $payee = Mockery::mock(User::class);
        $payee->shouldReceive('isLojista')->andReturn(false);
        $payee->shouldReceive('getBalance')->andReturn(0.00);

        $input = Mockery::mock(TransferInput::class);
        $input->shouldReceive('getPayer')->andReturn(1);
        $input->shouldReceive('getPayee')->andReturn(2);
        $input->shouldReceive('getValue')->andReturn(100.00);

        $this->userRepository->shouldReceive('getUserById')
            ->times(2)
            ->andReturnUsing(fn($id) => match ($id) {
                1 => $payer,
                2 => $payee
            });

        $this->useCase->execute($input);
    }

    public function testShouldThrowExceptionWhenPayerHasInsufficientBalance(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('O payer não tem saldo suficiente para realizar a transação.');

        $payer = Mockery::mock(User::class);
        $payer->shouldReceive('isLojista')->andReturn(false);
        $payer->shouldReceive('getBalance')->andReturn(50.00);

        $payee = Mockery::mock(User::class);
        $payee->shouldReceive('isLojista')->andReturn(false);
        $payee->shouldReceive('getBalance')->andReturn(0.00);

        $input = Mockery::mock(TransferInput::class);
        $input->shouldReceive('getPayer')->andReturn(1);
        $input->shouldReceive('getPayee')->andReturn(2);
        $input->shouldReceive('getValue')->andReturn(100.00);

        $this->userRepository->shouldReceive('getUserById')
            ->times(2)
            ->andReturnUsing(fn($id) => match ($id) {
                1 => $payer,
                2 => $payee
            });

        $this->useCase->execute($input);
    }

    public function testShouldExecuteTransferSuccessfully(): void
    {
        $payer = Mockery::mock(User::class);
        $payer->shouldReceive('isLojista')->andReturn(false);
        $payer->shouldReceive('getBalance')->andReturn(1000.00);

        $payee = Mockery::mock(User::class);
        $payee->shouldReceive('isLojista')->andReturn(false);
        $payee->shouldReceive('getBalance')->andReturn(0.00);

        $input = Mockery::mock(TransferInput::class);
        $input->shouldReceive('getPayer')->andReturn(1);
        $input->shouldReceive('getPayee')->andReturn(2);
        $input->shouldReceive('getValue')->andReturn(100.00);

        $this->userRepository->shouldReceive('getUserById')
            ->times(2)
            ->andReturnUsing(fn($id) => match ($id) {
                1 => $payer,
                2 => $payee
            });

        $this->authorizeService->shouldReceive('checkAuthorization')->andReturn(true);

        $this->useCase->execute($input);

        $this->assertTrue(true);
    }

    public function testShouldThrowExceptionWhenAuthorizationFails(): void
    {
        $this->expectException(UnauthorizedException::class);

        $this->authorizeService->shouldReceive('checkAuthorization')->andReturn(false);

        $payer = Mockery::mock(User::class);
        $payer->shouldReceive('isLojista')->andReturn(false);
        $payer->shouldReceive('getBalance')->andReturn(1000.00);

        $payee = Mockery::mock(User::class);
        $payee->shouldReceive('isLojista')->andReturn(false);
        $payee->shouldReceive('getBalance')->andReturn(0.00);

        $input = Mockery::mock(TransferInput::class);
        $input->shouldReceive('getPayer')->andReturn(1);
        $input->shouldReceive('getPayee')->andReturn(2);
        $input->shouldReceive('getValue')->andReturn(100.00);

        $this->userRepository->shouldReceive('getUserById')
            ->times(2)
            ->andReturnUsing(fn($id) => match ($id) {
                1 => $payer,
                2 => $payee
            });

        $this->useCase->execute($input);
    }
}
