<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Access\Permissions;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::CUSTOMERS_VIEW) || $user->can(Permissions::STAFF_VIEW);
    }

    public function view(User $user, User $target): bool
    {
        return $user->is($target) || $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::STAFF_MANAGE);
    }

    public function update(User $user, User $target): bool
    {
        return $user->can(Permissions::STAFF_MANAGE) || $user->is($target);
    }

    public function delete(User $user): bool
    {
        return $user->can(Permissions::STAFF_MANAGE);
    }

    public function manageRoles(User $user): bool
    {
        return $user->can(Permissions::STAFF_MANAGE);
    }

    public function suspend(User $user, User $target): bool
    {
        return $user->can(Permissions::STAFF_MANAGE) && ! $user->is($target);
    }

    public function restore(User $user): bool
    {
        return $user->can(Permissions::STAFF_MANAGE);
    }
}
