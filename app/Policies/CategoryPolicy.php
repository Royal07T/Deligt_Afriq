<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use App\Support\Access\Permissions;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::CATEGORIES_VIEW);
    }

    public function view(User $user, Category $category): bool
    {
        return $user->can(Permissions::CATEGORIES_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::CATEGORIES_CREATE);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->can(Permissions::CATEGORIES_EDIT);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->can(Permissions::CATEGORIES_DELETE);
    }
}
