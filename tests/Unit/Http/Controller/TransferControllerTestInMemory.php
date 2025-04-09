<?php

namespace Tests\Unit\Http\Controller;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Http\Controllers\TransferController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;
use App\ValueObjects\UserType;

class TransferControllerTestInMemory extends TestCase
{
    use RefreshDatabase;

    private User $payer;
    private User $payee;
    private Account $payerAccount;
    private Account $payeeAccount;
    private TransferController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create users with accounts in memory
        $this->payer = User::factory()->create([
            'user_type' => UserType::COMMON
        ]);
        $this->payee = User::factory()->create([
            'user_type' => UserType::COMMON
        ]);
        
        // Create accounts in memory
        $this->payerAccount = Account::factory()->create([
            'user_id' => $this->payer->id,
            'balance' => 1000 * 100
        ]);

        $this->payeeAccount = Account::factory()->create([
            'user_id' => $this->payee->id,
            'balance' => 500 * 100
        ]);
        
        // Link accounts to users
        $this->payer->update(['account_id' => $this->payerAccount->id]);
        $this->payee->update(['account_id' => $this->payeeAccount->id]);

        $this->controller = new TransferController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testUserCanBeCreatedWithoutAccount(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->account_id);
    }

    public function testAccountIdCannotBeChangedOnceSet(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->payer->update(['account_id' => $this->payeeAccount->id]);
    }

    public function testPayerMustExist(): void
    {
        $nonExistentId = $this->payer->id + 1000;
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $nonExistentId,
            'payee' => $this->payee->id,
            'value' => 100.00
        ]);

        $request->shouldReceive('input')
            ->with('payer')
            ->andReturn($nonExistentId);
        $request->shouldReceive('input')
            ->with('payee')
            ->andReturn($this->payee->id);
        $request->shouldReceive('input')
            ->with('value')
            ->andReturn(100.00);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ["Payer $nonExistentId não encontrado."]], $response->getData(true));
    }

    public function testPayeeMustExist(): void
    {
        $nonExistentId = $this->payee->id + 1000;
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => $nonExistentId,
            'value' => 100.00
        ]);

        $request->shouldReceive('input')
            ->with('payer')
            ->andReturn($this->payer->id);
        $request->shouldReceive('input')
            ->with('payee')
            ->andReturn($nonExistentId);
        $request->shouldReceive('input')
            ->with('value')
            ->andReturn(100.00);

        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ["Payee $nonExistentId não encontrado."]], $response->getData(true));
    }

    public function testValidTransferWithDatabase(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'value' => 100.00
        ]);
        
        $request->shouldReceive('input')
            ->with('payer')
            ->andReturn($this->payer->id);
        $request->shouldReceive('input')
            ->with('payee')
            ->andReturn($this->payee->id);
        $request->shouldReceive('input')
            ->with('value')
            ->andReturn(100.00);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['Transferência realizada com sucesso'], $response->getData(true));
    }
}
