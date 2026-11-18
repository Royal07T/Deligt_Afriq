<?php

namespace App\Policies;

use App\Models\ContentPage;
use App\Models\User;
use App\Support\Access\Permissions;

class ContentPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::CONTENT_VIEW);
    }

    public function view(User $user, ContentPage $page): bool
    {
        return $user->can(Permissions::CONTENT_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::CONTENT_MANAGE);
    }

    public function update(User $user, ContentPage $page): bool
    {
        return $user->can(Permissions::CONTENT_MANAGE);
    }

    public function delete(User $user, ContentPage $page): bool
    {
        return $user->can(Permissions::CONTENT_MANAGE);
    }
}
