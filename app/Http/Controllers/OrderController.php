<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Auth\Access\AuthorizationException;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    public function index()
    {
        return OrderResource::collection(
            auth()->user()->orders
        );
    }

    public function pending()
    {
        $this->authorize('viewAny', Order::class);

        return OrderResource::collection(
            Order::whereStatus(OrderStatus::REGISTERED)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = \DB::transaction(function () use ($request) {
            $from = Customer::create($request->from);
            $to = Customer::create($request->to);

            $order = new Order();
            $order->from_customer_id = $from->id;
            $order->to_customer_id = $to->id;
            $order->status = OrderStatus::REGISTERED;
            return \Auth::user()->orders()->save($order);
        });

        OrderCreated::dispatch($order);

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return new OrderResource($order);
    }

    public function cancel(Order $order)
    {
        $this->authorize('update', $order);

        if ($order->status !== OrderStatus::REGISTERED) {
            throw new AuthorizationException("Can't cancel active order");
        }

        $order->status = OrderStatus::CANCELLED;
        $order->save();

        OrderStatusUpdated::dispatch($order);

        return ['success' => true];
    }

    public function accept(Order $order)
    {
        $this->authorize('update', $order);

        if ($order->status !== OrderStatus::REGISTERED) {
            throw new AuthorizationException("Can't accept active order");
        }

        $order->status = OrderStatus::IN_PROGRESS;
        $order->save();

        OrderStatusUpdated::dispatch($order);

        return ['success' => true];
    }

    public function complete(Order $order)
    {
        $this->authorize('update', $order);

        if ($order->status !== OrderStatus::IN_PROGRESS) {
            throw new AuthorizationException("Can't complete active order");
        }

        $order->status = OrderStatus::COMPLETED;
        $order->save();

        OrderStatusUpdated::dispatch($order);

        return ['success' => true];
    }
}
