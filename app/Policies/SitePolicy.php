<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;

class SitePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, Site $site): bool
    {
        return $site->organization->owner_user_id === $user->id
            || $site->organization->memberships()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Site $site): bool
    {
        return $site->organization->owner_user_id === $user->id;
    }

    public function delete(User $user, Site $site): bool
    {
        return $site->organization->owner_user_id === $user->id;
    }
}
