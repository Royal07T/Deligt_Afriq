<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Access\Permissions;

class ActivityLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::ACTIVITY_LOGS_VIEW);
    }
}
