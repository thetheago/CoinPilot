<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\User;
use App\ValueObjects\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldReturnCorrectUserType(): void
    {
        $user = User::factory()->create(['user_type' => UserType::LOJISTA]);

        $this->assertInstanceOf(UserType::class, $user->getUserType());
        $this->assertEquals(UserType::LOJISTA, $user->getUserType()->getValue());
    }

    public function testShouldIdentifyLojistaUser(): void
    {
        $user = User::factory()->create(['user_type' => UserType::LOJISTA]);

        $this->assertTrue($user->isLojista());
        $this->assertFalse($user->isComum());
    }

    public function testShouldIdentifyComumUser(): void
    {
        $user = User::factory()->create(['user_type' => UserType::COMMON]);

        $this->assertTrue($user->isComum());
        $this->assertFalse($user->isLojista());
    }

    public function testShouldReturnAccountBalance(): void
    {
        $account = Account::factory()->create(['balance' => 100050]);
        $user = User::factory()->create(['account_id' => $account->id]);

        $this->assertEquals(100050, $user->getBalance());
    }

    public function testFactoryShouldCreateValidUser(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->cpf);
        $this->assertNotNull($user->user_type);
    }
}
