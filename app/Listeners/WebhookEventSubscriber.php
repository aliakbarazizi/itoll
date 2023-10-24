<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Events\OrderLocationCreated;
use App\Events\OrderStatusUpdated;
use App\Http\Resources\OrderLocationResource;
use App\Http\Resources\OrderResource;
use App\Models\User;
use Http;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookEventSubscriber
{
    public function handleOrderCreated(OrderCreated $event): void
    {
        $this->sendWebhook($event->order->user, new OrderResource(
            $event->order
        ));
    }

    public function handleOrderStatusUpdated(OrderStatusUpdated $event): void
    {
        $this->sendWebhook($event->order->user, new OrderResource(
            $event->order
        ));
    }

    public function handleOrderLocationCreated(OrderLocationCreated $event): void
    {
        $this->sendWebhook($event->orderLocation->order->user, new OrderLocationResource(
            $event->orderLocation
        ));
    }

    private function sendWebhook(User $user, JsonResource $payload): void
    {
        if (!$user->webhook) {
            return;
        }

        dispatch(function () use ($user, $payload) {
            try {
                Http::retry(3, 100)->post(
                    $user->webhook->url,
                    json_decode($payload->toJson())
                );
            } catch (\Throwable) {
                // maybe log the error
            }
        });
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            OrderCreated::class => 'handleOrderCreated',
            OrderStatusUpdated::class => 'handleOrderStatusUpdated',
            OrderLocationCreated::class => 'handleOrderLocationCreated'
        ];
    }
}
