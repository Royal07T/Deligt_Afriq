<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use App\Support\Access\Permissions;

class BannerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::BANNERS_VIEW);
    }

    public function view(User $user, Banner $banner): bool
    {
        return $user->can(Permissions::BANNERS_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::BANNERS_MANAGE);
    }

    public function update(User $user, Banner $banner): bool
    {
        return $user->can(Permissions::BANNERS_MANAGE);
    }

    public function delete(User $user, Banner $banner): bool
    {
        return $user->can(Permissions::BANNERS_MANAGE);
    }
}
