<?php

namespace App\Policies;

use App\Models\NewsletterSubscriber;
use App\Models\User;
use App\Support\Access\Permissions;

class NewsletterSubscriberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::NEWSLETTER_VIEW);
    }

    public function view(User $user, NewsletterSubscriber $subscriber): bool
    {
        return $user->can(Permissions::NEWSLETTER_VIEW);
    }

    public function delete(User $user, NewsletterSubscriber $subscriber): bool
    {
        return $user->can(Permissions::NEWSLETTER_MANAGE);
    }
}
