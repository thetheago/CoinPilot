<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\LogTransferService;
use Tests\TestCase;

class LogTransferServiceTest extends TestCase
{
    public function testInfo(): void
    {
        (new LogTransferService())->info('msg');

        $this->assertTrue(true);
    }

    public function testDebug(): void
    {
        (new LogTransferService())->debug('msg');

        $this->assertTrue(true);
    }

    public function testError(): void
    {
        (new LogTransferService())->error('msg');

        $this->assertTrue(true);
    }

    public function testWarning(): void
    {
        (new LogTransferService())->warning('msg');

        $this->assertTrue(true);
    }

    public function testCritical(): void
    {
        (new LogTransferService())->critical('msg');

        $this->assertTrue(true);
    }
}
