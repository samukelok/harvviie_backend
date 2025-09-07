<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Public access for viewing products
    }

    public function view(?User $user, Product $product): bool
    {
        return true; // Public access for viewing individual products
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->isAdmin(); // Only admins can delete
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->isAdmin(); // Only admins can restore
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete
    }
}