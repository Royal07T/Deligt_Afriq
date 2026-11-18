<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;
use App\Support\Access\Permissions;

class InventoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::INVENTORY_VIEW);
    }

    public function view(User $user, Inventory $inventory): bool
    {
        return $user->can(Permissions::INVENTORY_VIEW);
    }

    public function manage(User $user, Inventory $inventory): bool
    {
        return $user->can(Permissions::INVENTORY_MANAGE);
    }
}
