<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Repositories\UserRepository;
use App\Models\User;
use App\Exceptions\UserNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\ValueObjects\UserType;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new UserRepository();
    }

    public function testGetUserByIdReturnsUserWhenFound(): void
    {
        $user = User::factory()->create([
            'user_type' => UserType::COMMON
        ]);

        $result = $this->repository->getUserById($user->id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertEquals($user->name, $result->name);
        $this->assertEquals($user->email, $result->email);
    }

    public function testGetUserByIdThrowsExceptionWhenUserNotFound(): void
    {
        $userId = fake()->numberBetween(1000, 9999);
        
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("Usuário $userId não encontrado");

        $this->repository->getUserById($userId);
    }

    public function testIsUserLojistaReturnsTrueForLojista(): void
    {
        $user = User::factory()->create([
            'user_type' => UserType::LOJISTA
        ]);

        $result = $this->repository->isUserLojista($user->id);

        $this->assertTrue($result);
    }

    public function testIsUserLojistaReturnsFalseForCommonUser(): void
    {
        $user = User::factory()->create([
            'user_type' => UserType::COMMON
        ]);

        $result = $this->repository->isUserLojista($user->id);

        $this->assertFalse($result);
    }

    public function testIsUserLojistaThrowsExceptionWhenUserNotFound(): void
    {
        $userId = fake()->numberBetween(1000, 9999);
        
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage("Usuário $userId não encontrado");

        $this->repository->isUserLojista($userId);
    }
}
