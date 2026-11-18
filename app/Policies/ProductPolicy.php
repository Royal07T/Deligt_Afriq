<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Support\Access\Permissions;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::PRODUCTS_VIEW);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->can(Permissions::PRODUCTS_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::PRODUCTS_CREATE);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->can(Permissions::PRODUCTS_EDIT);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->can(Permissions::PRODUCTS_DELETE);
    }

    public function manageInventory(User $user, Product $product): bool
    {
        return $user->can(Permissions::INVENTORY_MANAGE);
    }
}
