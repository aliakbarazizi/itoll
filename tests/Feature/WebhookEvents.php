<?php

namespace Tests\Feature;

use App\Events\OrderCreated;
use App\Events\OrderLocationCreated;
use App\Events\OrderStatusUpdated;
use App\Listeners\WebhookEventSubscriber;
use App\Models\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderLocation;
use App\Models\User;
use App\Models\Webhook;
use Http;
use Illuminate\Http\Client\Request;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class WebhookEvents extends TestCase
{
    public function test_order_created(): void
    {
        Http::fake([
            'https://example.com' => Http::response(),
        ]);

        $user = User::factory()
            ->has(Webhook::factory()->state(['url' => 'https://example.com']))
            ->create();

        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::REGISTERED
        ]);

        $listener = new WebhookEventSubscriber();

        $listener->handleOrderCreated(new OrderCreated($order));

        Http::assertSentCount(1);
        $this->assertOrder();
    }

    public function test_order_status_updated(): void
    {
        Http::fake([
            'https://example.com' => Http::response(),
        ]);

        $user = User::factory()
            ->has(Webhook::factory()->state(['url' => 'https://example.com']))
            ->create();

        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::REGISTERED
        ]);

        $listener = new WebhookEventSubscriber();

        $listener->handleOrderStatusUpdated(new OrderStatusUpdated($order));

        Http::assertSentCount(1);
        $this->assertOrder();
    }

    public function test_location_created(): void
    {
        Http::fake([
            'https://example.com' => Http::response(),
        ]);

        $user = User::factory()
            ->has(Webhook::factory()->state(['url' => 'https://example.com']))
            ->create();

        $orderLocation = OrderLocation::factory()->recycle($user)->create();

        $listener = new WebhookEventSubscriber();

        $listener->handleOrderLocationCreated(new OrderLocationCreated($orderLocation));

        Http::assertSentCount(1);
        Http::assertSent(function (Request $request) {
            AssertableJson::fromArray($request->data())
                ->whereType('id', 'integer')
                ->whereType('order_id', 'integer')
                ->whereType('latitude', 'double')
                ->whereType('longitude', 'double')
                ->interacted();
            return true;
        });
    }


    public function test_retry_webhook(): void
    {
        Http::fake([
            'https://example.com' => Http::sequence()
                ->push(status: 500)
                ->push(status: 500)
                ->push(),
        ]);

        $user = User::factory()
            ->has(Webhook::factory()->state(['url' => 'https://example.com']))
            ->create();

        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::REGISTERED
        ]);

        $listener = new WebhookEventSubscriber();

        $listener->handleOrderCreated(new OrderCreated($order));

        Http::assertSentCount(3);
        Http::assertSequencesAreEmpty();
    }

    public function test_fail_webhook(): void
    {
        Http::fake([
            'https://example.com' => Http::sequence()
                ->push(status: 500)
                ->push(status: 500)
                ->push(status: 500)
                ->push()
        ]);

        $user = User::factory()
            ->has(Webhook::factory()->state(['url' => 'https://example.com']))
            ->create();

        $order = Order::factory()->for($user)->create([
            'status' => OrderStatus::REGISTERED
        ]);

        $listener = new WebhookEventSubscriber();

        $listener->handleOrderCreated(new OrderCreated($order));

        Http::assertSentCount(3);
    }


    private function assertOrder(): void
    {
        Http::assertSent(function (Request $request) {
            AssertableJson::fromArray($request->data())
                ->whereType('id', 'integer')
                ->whereType('from_customer.name', 'string')
                ->whereType('from_customer.mobile', 'string')
                ->whereType('from_customer.address', 'string')
                ->whereType('from_customer.latitude', 'double')
                ->whereType('from_customer.longitude', 'double')
                ->whereType('to_customer.name', 'string')
                ->whereType('to_customer.mobile', 'string')
                ->whereType('to_customer.address', 'string')
                ->whereType('to_customer.latitude', 'double')
                ->whereType('to_customer.longitude', 'double')
                ->where('status', OrderStatus::REGISTERED->value)
                ->interacted();
            return true;
        });
    }
}
