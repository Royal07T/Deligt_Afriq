<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Access\Permissions;
use App\Support\Access\Roles;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::ROLES_VIEW);
    }

    public function view(User $user): bool
    {
        return $user->can(Permissions::ROLES_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::ROLES_MANAGE);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can(Permissions::ROLES_MANAGE) && ! in_array($role->name, Roles::PROTECTED, true);
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->can(Permissions::ROLES_MANAGE) && ! in_array($role->name, Roles::PROTECTED, true);
    }
}
