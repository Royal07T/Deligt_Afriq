<?php

namespace App\Policies;

use App\Models\HomepageSection;
use App\Models\User;
use App\Support\Access\Permissions;

class HomepageSectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::HOMEPAGE_VIEW);
    }

    public function view(User $user, HomepageSection $section): bool
    {
        return $user->can(Permissions::HOMEPAGE_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::HOMEPAGE_MANAGE);
    }

    public function update(User $user, HomepageSection $section): bool
    {
        return $user->can(Permissions::HOMEPAGE_MANAGE);
    }

    public function delete(User $user, HomepageSection $section): bool
    {
        return $user->can(Permissions::HOMEPAGE_MANAGE);
    }
}
