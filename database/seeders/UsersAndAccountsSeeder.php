<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use App\ValueObjects\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersAndAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::create([
            'name' => 'Tim Henson',
            'email' => 'tim@henson.com',
            'password' => Hash::make('pass'),
            'cpf' => fake('pt_BR')->unique()->cpf(),
            'user_type' => UserType::COMMON,
        ]);

        $account1 = Account::create([
            'user_id' => $user1->id,
            'balance' => 38515, // 385,15 reais
        ]);

        $user1->account_id = $account1->id;
        $user1->save();

        $user2 = User::create([
            'name' => 'Scott Lepage',
            'email' => 'scott@lepage.com',
            'password' => Hash::make('pass'),
            'cpf' => fake('pt_BR')->unique()->cpf(),
            'user_type' => UserType::COMMON,
        ]);

        $account2 = Account::create([
            'user_id' => $user2->id,
            'balance' => 80090, // 800,90 reais
        ]);

        $user2->account_id = $account2->id;
        $user2->save();

        $user3 = User::create([
            'name' => 'Clay Sober',
            'email' => 'clay@sober.com',
            'password' => Hash::make('pass'),
            'cpf' => fake('pt_BR')->unique()->cpf(),
            'user_type' => UserType::LOJISTA,
        ]);

        $account3 = Account::create([
            'user_id' => $user3->id,
            'balance' => 20901, // 209,01 reais
        ]);

        $user3->account_id = $account3->id;
        $user3->save();
    }
} 