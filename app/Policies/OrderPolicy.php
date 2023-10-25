<?php

namespace App\Policies;

use App\Models\Enums\OrderStatus;
use App\Models\Enums\UserType;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserType::USER;
    }

    public function viewDriver(User $user): bool
    {
        return $user->role === UserType::DRIVER;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->role === UserType::USER ? $user->id === $order->user_id : $user->id === $order->driver_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserType::USER;
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && $order->status === OrderStatus::REGISTERED;
    }

    public function accept(User $user, Order $order): bool
    {
        return $user->role === UserType::DRIVER && $order->status === OrderStatus::REGISTERED;
    }

    public function complete(User $user, Order $order): bool
    {
        return $user->id === $order->driver_id && $order->status === OrderStatus::IN_PROGRESS;
    }
}
