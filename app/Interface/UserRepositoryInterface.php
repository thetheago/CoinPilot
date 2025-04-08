<?php

declare(strict_types=1);

namespace App\Interface;

use App\Models\User;
use App\Exceptions\UserNotFoundException;

interface UserRepositoryInterface
{
    public function isUserLojista(int $userId): bool;

    /**
     * @throws UserNotFoundException
     */
    public function getUserById(int $userId): User;
}
