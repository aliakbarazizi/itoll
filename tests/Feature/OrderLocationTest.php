<?php

namespace Tests\Feature;

use App\Events\OrderLocationCreated;
use App\Models\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderLocationTest extends TestCase
{
    public function test_update_live_location(): void
    {
        Event::fake();

        Sanctum::actingAs(User::factory()->driver()->create());

        $order = Order::factory()->for(Auth::user(), 'driver')->create([
            'status' => OrderStatus::IN_PROGRESS
        ]);

        for ($i = 0; $i < 10; $i++) {
            $location = [
                'latitude' => fake()->latitude,
                'longitude' => fake()->longitude
            ];

            $this
                ->postJson("/api/orders/$order->id/locations", $location)
                ->assertStatus(201)
                ->assertJson(fn(AssertableJson $json) => $json
                    ->whereType('id', 'integer')
                    ->where('order_id', $order->id)
                    ->where('latitude', $location['latitude'])
                    ->where('longitude', $location['longitude'])
                );
        }

        $this->assertEquals(10, $order->orderLocations->count());

        Event::assertDispatched(OrderLocationCreated::class, 10);
    }
}
