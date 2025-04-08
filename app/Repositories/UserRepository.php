<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Interface\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\UserNotFoundException;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @throws UserNotFoundException
     */
    public function getUserById(int $userId): User
    {
        try {
            return User::findOrFail($userId);
        } catch (ModelNotFoundException $e) {
            throw new UserNotFoundException("Usuário $userId não encontrado");
        }
    }

    /**
     * @throws UserNotFoundException
     */
    public function isUserLojista(int $userId): bool
    {
        return $this->getUserById($userId)->isLojista();
    }
}