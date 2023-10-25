<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Enums\OrderStatus;
use App\Models\Order;
use Auth;
use DB;

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

    public function driver()
    {
        $this->authorize('viewDriver', Order::class);

        return OrderResource::collection(
            Order::whereDriverId(Auth::id())->get()
        );
    }

    public function pending()
    {
        $this->authorize('viewDriver', Order::class);

        return OrderResource::collection(
            Order::whereStatus(OrderStatus::REGISTERED)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = DB::transaction(function () use ($request) {
            $from = Customer::create($request->from);
            $to = Customer::create($request->to);

            $order = new Order();
            $order->from_customer_id = $from->id;
            $order->to_customer_id = $to->id;
            $order->status = OrderStatus::REGISTERED;
            return Auth::user()->orders()->save($order);
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

    public function cancel(int $order): array
    {
        return $this->changeOrderStatus($order, 'cancel', OrderStatus::CANCELLED);
    }

    public function accept(int $order): array
    {
        return $this->changeOrderStatus($order, 'accept', OrderStatus::IN_PROGRESS);
    }

    public function complete(int $order): array
    {
        return $this->changeOrderStatus($order, 'complete', OrderStatus::COMPLETED);
    }

    private function changeOrderStatus(int $id, string $ability, OrderStatus $status): array
    {
        $order = DB::transaction(function () use ($id, $ability, $status) {
            $order = Order::lockForUpdate()->findOrFail($id,);

            $this->authorize($ability, $order);

            $order->status = $status;
            $order->save();

            return $order;
        });

        OrderStatusUpdated::dispatch($order);

        return ['success' => true];
    }
}
