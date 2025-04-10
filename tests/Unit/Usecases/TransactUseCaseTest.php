<?php

declare(strict_types=1);

namespace Tests\Unit\Usecases;

use App\Dto\TransferInput;
use App\Exceptions\LojistAsAPayerException;
use App\Exceptions\NotEnoughCashException;
use App\Interface\IUserRepository;
use App\Interface\IAuthorizeService;
use App\Models\User;
use App\Usecases\TransactUseCase;
use Tests\TestCase;
use App\Exceptions\UnauthorizedException;
use Mockery;
use App\Jobs\TransferJob;
use Illuminate\Support\Facades\Queue;

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
        $payer->shouldReceive('getBalance')->andReturn(fake()->randomFloat(2, 1000, 10000));

        $payee = Mockery::mock(User::class);
        $payee->shouldReceive('isLojista')->andReturn(false);
        $payee->shouldReceive('getBalance')->andReturn(fake()->randomFloat(2, 0, 1000));

        $input = Mockery::mock(TransferInput::class);
        $payerId = fake()->numberBetween(1, 100);
        $payeeId = fake()->numberBetween(1, 100);
        $input->shouldReceive('getPayer')->andReturn($payerId);
        $input->shouldReceive('getPayee')->andReturn($payeeId);
        $input->shouldReceive('getValue')->andReturn(fake()->randomFloat(2, 0.01, 1000));

        /** @disregard */
        $this->userRepository->shouldReceive('getUserById')
            ->times(2)
            ->andReturnUsing(fn($id) => match ($id) {
                $payerId => $payer,
                $payeeId => $payee
            });

        $this->useCase->execute($input);
    }

    public function testShouldThrowExceptionWhenPayerHasInsufficientBalance(): void
    {
        $this->expectException(NotEnoughCashException::class);
        $this->expectExceptionMessage('Saldo insuficiente para a transferência.');

        $payer = Mockery::mock(User::class);
        $payer->shouldReceive('isLojista')->andReturn(false);
        $payer->shouldReceive('getBalance')->andReturn(fake()->randomFloat(2, 0, 50));

        $payee = Mockery::mock(User::class);
        $payee->shouldReceive('isLojista')->andReturn(false);
        $payee->shouldReceive('getBalance')->andReturn(fake()->randomFloat(2, 0, 1000));

        $input = Mockery::mock(TransferInput::class);
        $payerId = fake()->numberBetween(1, 100);
        $payeeId = fake()->numberBetween(1, 100);
        $input->shouldReceive('getPayer')->andReturn($payerId);
        $input->shouldReceive('getPayee')->andReturn($payeeId);
        $input->shouldReceive('getValue')->andReturn(fake()->randomFloat(2, 100, 1000));

        /** @disregard */
        $this->userRepository->shouldReceive('getUserById')
            ->times(2)
            ->andReturnUsing(fn($id) => match ($id) {
                $payerId => $payer,
                $payeeId => $payee
            });

        $this->useCase->execute($input);
    }

    public function testShouldExecuteTransferSuccessfully(): void
    {
        Queue::fake();

        $payer = Mockery::mock(User::class);
        $payer->shouldReceive('isLojista')->andReturn(false);
        $payer->shouldReceive('getBalance')->andReturn(fake()->randomFloat(2, 1000, 10000));

        $payee = Mockery::mock(User::class);
        $payee->shouldReceive('isLojista')->andReturn(false);
        $payee->shouldReceive('getBalance')->andReturn(fake()->randomFloat(2, 0, 1000));

        $input = Mockery::mock(TransferInput::class);
        $payerId = fake()->numberBetween(1, 100);
        $payeeId = fake()->numberBetween(1, 100);
        $value = fake()->randomFloat(2, 0.01, 1000);
        $input->shouldReceive('getPayer')->andReturn($payerId);
        $input->shouldReceive('getPayee')->andReturn($payeeId);
        $input->shouldReceive('getValue')->andReturn($value);

        /** @disregard */
        $this->userRepository->shouldReceive('getUserById')
            ->times(2)
            ->andReturnUsing(fn($id) => match ($id) {
                $payerId => $payer,
                $payeeId => $payee
            });

        /** @disregard */
        $this->authorizeService->shouldReceive('checkAuthorization')->andReturn(true);

        $this->useCase->execute($input);

        Queue::assertPushed(TransferJob::class);
    }

    public function testShouldThrowExceptionWhenAuthorizationFails(): void
    {
        $this->expectException(UnauthorizedException::class);

        /** @disregard */
        $this->authorizeService->shouldReceive('checkAuthorization')->andReturn(false);

        $payer = Mockery::mock(User::class);
        $payer->shouldReceive('isLojista')->andReturn(false);
        $payer->shouldReceive('getBalance')->andReturn(fake()->randomFloat(2, 1000, 10000));

        $payee = Mockery::mock(User::class);
        $payee->shouldReceive('isLojista')->andReturn(false);
        $payee->shouldReceive('getBalance')->andReturn(fake()->randomFloat(2, 0, 1000));

        $input = Mockery::mock(TransferInput::class);
        $payerId = fake()->numberBetween(1, 100);
        $payeeId = fake()->numberBetween(1, 100);
        $input->shouldReceive('getPayer')->andReturn($payerId);
        $input->shouldReceive('getPayee')->andReturn($payeeId);
        $input->shouldReceive('getValue')->andReturn(fake()->randomFloat(2, 0.01, 1000));

        /** @disregard */
        $this->userRepository->shouldReceive('getUserById')
            ->times(2)
            ->andReturnUsing(fn($id) => match ($id) {
                $payerId => $payer,
                $payeeId => $payee
            });

        $this->useCase->execute($input);
    }
}
