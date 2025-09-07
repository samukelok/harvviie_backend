<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;

class CollectionPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Public access for viewing collections
    }

    public function view(?User $user, Collection $collection): bool
    {
        return true; // Public access for viewing individual collections
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function update(User $user, Collection $collection): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }

    public function delete(User $user, Collection $collection): bool
    {
        return $user->isAdmin(); // Only admins can delete
    }

    public function restore(User $user, Collection $collection): bool
    {
        return $user->isAdmin(); // Only admins can restore
    }

    public function forceDelete(User $user, Collection $collection): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete
    }
}