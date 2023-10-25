<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()
            ->create([
                'role' => UserType::USER,
            ]);

        $user->createToken("access_token");

        $user = User::factory()
            ->create([
                'role' => UserType::DRIVER,
            ]);

        $user->createToken("access_token");
    }
}
