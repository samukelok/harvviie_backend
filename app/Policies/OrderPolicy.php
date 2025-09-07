<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function view(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function update(User $user, Order $order): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->isAdmin(); // Only admins can cancel orders
    }
}