<?php

namespace App\Policies;

use App\Models\GalleryItem;
use App\Models\User;
use App\Support\Access\Permissions;

class GalleryItemPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::GALLERY_VIEW);
    }

    public function view(User $user, GalleryItem $galleryItem): bool
    {
        return $user->can(Permissions::GALLERY_VIEW);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::GALLERY_MANAGE);
    }

    public function update(User $user, GalleryItem $galleryItem): bool
    {
        return $user->can(Permissions::GALLERY_MANAGE);
    }

    public function delete(User $user, GalleryItem $galleryItem): bool
    {
        return $user->can(Permissions::GALLERY_MANAGE);
    }
}
