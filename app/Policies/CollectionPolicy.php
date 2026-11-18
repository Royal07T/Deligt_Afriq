<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;
use App\Support\Access\Permissions;

class CollectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::COLLECTIONS_VIEW);
    }

    public function view(User $user, Collection $collection): bool
    {
        return $user->can(Permissions::COLLECTIONS_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::COLLECTIONS_CREATE);
    }

    public function update(User $user, Collection $collection): bool
    {
        return $user->can(Permissions::COLLECTIONS_EDIT);
    }

    public function delete(User $user, Collection $collection): bool
    {
        return $user->can(Permissions::COLLECTIONS_DELETE);
    }
}
