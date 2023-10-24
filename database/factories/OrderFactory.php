<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_customer_id' => Customer::factory(),
            'to_customer_id' => Customer::factory(),
            'status' => fake()->randomElement(OrderStatus::cases())->value,
            'user_id' => User::factory(),
        ];
    }
}
