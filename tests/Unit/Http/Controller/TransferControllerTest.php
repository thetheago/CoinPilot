<?php

namespace Tests\Unit\Http\Controller;

use Tests\TestCase;
use App\Http\Controllers\TransferController;
use Illuminate\Http\Request;
use Mockery;

class TransferControllerTest extends TestCase
{
    private TransferController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new TransferController();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testTransferRequiresPayerField(): void
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('all')->andReturn([
            'payee' => fake()->numberBetween(1, 100),
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
            'payer' => fake()->numberBetween(1, 100),
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
            'payer' => fake()->numberBetween(1, 100),
            'payee' => fake()->numberBetween(1, 100)
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
            'payee' => fake()->numberBetween(1, 100),
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
            'payer' => fake()->numberBetween(1, 100),
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
            'payer' => fake()->numberBetween(1, 100),
            'payee' => fake()->numberBetween(1, 100),
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
            'payer' => fake()->numberBetween(1, 100),
            'payee' => fake()->numberBetween(1, 100),
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
            'payer' => fake()->numberBetween(1, 100),
            'payee' => fake()->numberBetween(1, 100),
            'value' => 30.241
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
}
