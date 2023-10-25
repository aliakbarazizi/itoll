<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Models\Customer;
use App\Models\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Auth;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_create_order(): void
    {
        Event::fake();
        Sanctum::actingAs(User::factory()->create());

        /** @var Customer $fromCustomer */
        $fromCustomer = Customer::factory()->make();
        /** @var Customer $toCustomer */
        $toCustomer = Customer::factory()->make();

        $request = [
            'from' => [
                'name' => $fromCustomer->name,
                'mobile' => $fromCustomer->mobile,
                'address' => $fromCustomer->address,
                'latitude' => $fromCustomer->latitude,
                'longitude' => $fromCustomer->longitude,
            ],
            'to' => [
                'name' => $toCustomer->name,
                'mobile' => $toCustomer->mobile,
                'address' => $toCustomer->address,
                'latitude' => $toCustomer->latitude,
                'longitude' => $toCustomer->longitude,
            ],
        ];

        $this
            ->postJson('/api/orders', $request)
            ->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) => $json
                ->whereType('id', 'integer')
                ->where('from_customer', $request['from'])
                ->where('to_customer', $request['to'])
                ->where('status', OrderStatus::REGISTERED->value)
            );

        Event::assertDispatched(OrderCreated::class, 1);
    }

    public function test_create_order_by_driver(): void
    {
        Sanctum::actingAs(User::factory()->driver()->create());

        $this
            ->postJson('/api/orders')
            ->assertStatus(403);
    }

    public function test_cancel_order(): void
    {
        Event::fake();
        Sanctum::actingAs(User::factory()->create());

        $order = Order::factory()->for(Auth::user())->create([
            'status' => OrderStatus::REGISTERED
        ]);

        $this
            ->putJson("/api/orders/$order->id/cancel")
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('success', true)
            );

        $this->assertEquals(OrderStatus::CANCELLED, $order->fresh()->status);
        Event::assertDispatched(OrderStatusUpdated::class, 1);
    }

    public function test_prevent_cancel_active_order(): void
    {
        Event::fake();
        Sanctum::actingAs(User::factory()->create());

        $order = Order::factory()->for(Auth::user())->create([
            'status' => OrderStatus::IN_PROGRESS
        ]);

        $this
            ->putJson("/api/orders/$order->id/cancel")
            ->assertStatus(403);
    }

    public function test_accept_order(): void
    {
        Event::fake();
        Sanctum::actingAs(User::factory()->driver()->create());

        $order = Order::factory()->for(Auth::user(), 'driver')->create([
            'status' => OrderStatus::REGISTERED
        ]);

        $this
            ->putJson("/api/orders/$order->id/accept")
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('success', true)
            );

        $this->assertEquals(OrderStatus::IN_PROGRESS, $order->fresh()->status);
        Event::assertDispatched(OrderStatusUpdated::class, 1);
    }

    public function test_complete_order(): void
    {
        Event::fake();
        Sanctum::actingAs(User::factory()->driver()->create());

        $order = Order::factory()->for(Auth::user(), 'driver')->create([
            'status' => OrderStatus::IN_PROGRESS
        ]);

        $this
            ->putJson("/api/orders/$order->id/complete")
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('success', true)
            );

        $this->assertEquals(OrderStatus::COMPLETED, $order->fresh()->status);
        Event::assertDispatched(OrderStatusUpdated::class, 1);
    }

    public function test_get_list_of_orders(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Order::factory()
            ->count(10)
            ->create();

        $order = Order::factory()->for(Auth::user())->count(2)->create();

        $this
            ->getJson("/api/orders")
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has(2)
                ->first(fn(AssertableJson $json) => $json
                    ->where('id', $order[0]->id)
                    ->etc()
                )
            );
    }

    public function test_get_list_of_pending_orders(): void
    {
        Sanctum::actingAs(User::factory()->driver()->create());

        Order::factory()
            ->count(3)
            ->create(
                new Sequence(
                    ['status' => OrderStatus::COMPLETED],
                    ['status' => OrderStatus::CANCELLED],
                    ['status' => OrderStatus::IN_PROGRESS]
                )
            );

        $order = Order::factory()->count(2)->create([
            'status' => OrderStatus::REGISTERED
        ]);

        $this
            ->getJson("/api/orders/pending")
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has(2)
                ->first(fn(AssertableJson $json) => $json
                    ->where('status', OrderStatus::REGISTERED->value)
                    ->where('id', $order[0]->id)
                    ->etc()
                )
            );
    }

    public function test_get_list_of_driver_orders(): void
    {
        Sanctum::actingAs(User::factory()->driver()->create());

        Order::factory()
            ->count(10)
            ->create();

        $order = Order::factory()->for(Auth::user(), 'driver')->count(2)->create();

        $this
            ->getJson("/api/orders/driver")
            ->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has(2)
                ->first(fn(AssertableJson $json) => $json
                    ->where('id', $order[0]->id)
                    ->etc()
                )
            );
    }
}
