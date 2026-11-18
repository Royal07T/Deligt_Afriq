<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use App\Support\Access\Permissions;
use App\Support\Orders\OrderStatus;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::ORDERS_VIEW);
    }

    public function view(User $user, Order $order): bool
    {
        return $order->user_id === $user->id || $user->can(Permissions::ORDERS_VIEW);
    }

    public function update(User $user, Order $order): bool
    {
        return $user->can(Permissions::ORDERS_UPDATE);
    }

    public function cancel(User $user, Order $order): bool
    {
        if ($order->user_id === $user->id) {
            return in_array($order->status, [OrderStatus::PENDING, OrderStatus::AWAITING_PAYMENT], true);
        }

        return $user->can(Permissions::ORDERS_CANCEL);
    }
}
