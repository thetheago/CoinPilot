<?php

namespace Tests\Unit\Jobs;

use App\Jobs\TransferJob;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Mockery;
use App\Repositories\EventsRepository;

class TransferJobTest extends TestCase
{
    private EventsRepository $eventsRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->eventsRepository = Mockery::mock(EventsRepository::class);
    }

    public function testJobCanBeCreated(): void
    {
        $payer = Mockery::mock(User::class);
        $payee = Mockery::mock(User::class);
        $amount = 1000;

        $payer->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $payee->shouldReceive('getAttribute')->with('id')->andReturn(2);

        $job = new TransferJob($payer, $payee, $amount, $this->eventsRepository);

        $this->assertInstanceOf(TransferJob::class, $job);
        $this->assertEquals(1, $job->payer->id);
        $this->assertEquals(2, $job->payee->id);
        $this->assertEquals($amount, $job->balance);
    }

    public function testJobCanBeDispatched(): void
    {
        Queue::fake();

        $payer = Mockery::mock(User::class);
        $payee = Mockery::mock(User::class);
        $amount = 1000;

        TransferJob::dispatch($payer, $payee, $amount, $this->eventsRepository);

        Queue::assertPushed(TransferJob::class, function ($job) use ($payer, $payee, $amount) {
            return $job->payer === $payer &&
                   $job->payee === $payee &&
                   $job->balance === $amount;
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
