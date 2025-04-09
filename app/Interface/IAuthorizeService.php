<?php

declare(strict_types=1);

namespace App\Interface;

interface IAuthorizeService
{
    public function checkAuthorization(): bool;
}
