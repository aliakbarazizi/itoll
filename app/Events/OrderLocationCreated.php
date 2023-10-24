<?php

namespace App\Events;

use App\Models\OrderLocation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderLocationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public OrderLocation $orderLocation)
    {
    }
}
