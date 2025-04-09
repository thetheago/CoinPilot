<?php

namespace Tests\Unit\Jobs;

use App\Jobs\TransferJob;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Mockery;

class TransferJobTest extends TestCase
{
    public function testJobCanBeCreated(): void
    {
        $payer = Mockery::mock(User::class);
        $payee = Mockery::mock(User::class);
        $amount = 1000;

        $payer->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $payee->shouldReceive('getAttribute')->with('id')->andReturn(2);

        $job = new TransferJob($payer, $payee, $amount);

        $this->assertInstanceOf(TransferJob::class, $job);
        $this->assertEquals(1, $job->payer->id);
        $this->assertEquals(2, $job->payee->id);
        $this->assertEquals($amount, $job->amount);
    }

    public function testJobHandleMethodLogsInfo(): void
    {
        Log::shouldReceive('info')
            ->once()
            ->with('TransferJob');

        $payer = Mockery::mock(User::class);
        $payee = Mockery::mock(User::class);
        $amount = 1000;

        $job = new TransferJob($payer, $payee, $amount);
        $job->handle();

        $this->assertTrue(true);
    }

    public function testJobCanBeDispatched(): void
    {
        Queue::fake();

        $payer = Mockery::mock(User::class);
        $payee = Mockery::mock(User::class);
        $amount = 1000;

        TransferJob::dispatch($payer, $payee, $amount);

        Queue::assertPushed(TransferJob::class, function ($job) use ($payer, $payee, $amount) {
            return $job->payer === $payer &&
                   $job->payee === $payee &&
                   $job->amount === $amount;
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
