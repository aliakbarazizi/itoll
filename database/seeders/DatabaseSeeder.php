<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Enums\UserType;
use App\Models\Order;
use App\Models\OrderLocation;
use App\Models\Webhook;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()
            ->has(Webhook::factory())
            ->count(10)
            ->create([
                'role' => UserType::USER,
            ]);

        \App\Models\User::factory()
            ->has(
                Order::factory()
                    ->has(OrderLocation::factory()->count(10))
                    ->count(10)
            )
            ->
            create([
                'role' => UserType::DRIVER,
            ]);
    }
}
