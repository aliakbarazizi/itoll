<?php

namespace App\Http\Controllers;

use App\Events\OrderLocationCreated;
use App\Http\Requests\StoreOrderLocationRequest;
use App\Http\Resources\OrderLocationResource;
use App\Models\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Validation\UnauthorizedException;

class OrderLocationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Order $order, StoreOrderLocationRequest $request)
    {
        $this->authorize('viewDriver', $order);

        if ($order->status !== OrderStatus::IN_PROGRESS) {
            throw new UnauthorizedException("Order is not active");
        }

        $location = $order->orderLocations()->create($request->validated());

        OrderLocationCreated::dispatch($location);

        return new OrderLocationResource($location);
    }
}
