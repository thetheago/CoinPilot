<?php

namespace Tests\Unit\Http\Controller;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Http\Controllers\TransferController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery;

class TransferControllerTest extends TestCase
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
        
        $this->payer = User::factory()->create();
        $this->payee = User::factory()->create();
        
        $this->payerAccount = Account::factory()->create(['user_id' => $this->payer->id]);
        $this->payeeAccount = Account::factory()->create(['user_id' => $this->payee->id]);
        
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

    public function testTransferRequiresPayerField(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payee' => $this->payee->id,
            'value' => fake()->randomFloat(2, 0.01, 1000)
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ['O campo payer é obrigatório.']], $response->getData(true));
    }

    public function testTransferRequiresPayeeField(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'value' => fake()->randomFloat(2, 0.01, 1000)
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ['O campo payee é obrigatório.']], $response->getData(true));
    }

    public function testTransferRequiresValueField(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ['O campo value é obrigatório.']], $response->getData(true));
    }

    public function testPayerMustBeAnInteger(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => 'seu madruga',
            'payee' => $this->payee->id,
            'value' => fake()->randomFloat(2, 0.01, 1000)
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ['O ID do payer deve ser um número inteiro.']], $response->getData(true));
    }

    public function testPayeeMustBeAnInteger(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => 'seu barriga',
            'value' => fake()->randomFloat(2, 0.01, 1000)
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ['O ID do payee deve ser um número inteiro.']], $response->getData(true));
    }

    public function testValueMustBeNumeric(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'value' => 'timao e pumba'
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => [
            'O campo value deve ser um número.',
            'O campo value deve ser um número com duas casas decimais.'
            ]
        ], $response->getData(true));
    }

    public function testValueMustBeGreaterThanZero(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'value' => 0
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ['O campo value deve ser maior que zero.']], $response->getData(true));
    }

    public function testValueMustHaveAtMostTwoDecimalPlaces(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'value' => fake()->randomFloat(3, 0.01, 1000)
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(
            [
                'errors' => ['O campo value deve ser um número com duas casas decimais.']
            ],
            $response->getData(true)
        );
    }

    public function testPayerMustExist(): void
    {
        $nonExistentId = $this->payer->id + fake()->numberBetween(1, 1000);
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $nonExistentId,
            'payee' => $this->payee->id,
            'value' => fake()->randomFloat(2, 0.01, 1000)
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ['O payer informado não existe.']], $response->getData(true));
    }

    public function testPayeeMustExist(): void
    {
        $nonExistentId = $this->payer->id + fake()->numberBetween(1, 1000);
        
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => $nonExistentId,
            'value' => fake()->randomFloat(2, 0.01, 1000)
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals(['errors' => ['O payee informado não existe.']], $response->getData(true));
    }

    public function testValidTransferRequest(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payer' => $this->payer->id,
            'payee' => $this->payee->id,
            'value' => fake()->randomFloat(2, 0.01, 1000)
        ]);
        
        $response = $this->controller->transfer($request);
        
        $this->assertEquals(200, $response->getStatusCode());
    }
}
