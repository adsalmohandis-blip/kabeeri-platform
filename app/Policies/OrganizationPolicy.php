<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, Organization $organization): bool
    {
        return $organization->owner_user_id === $user->id
            || $organization->memberships()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Organization $organization): bool
    {
        return $organization->owner_user_id === $user->id;
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $organization->owner_user_id === $user->id;
    }

    public function restore(User $user, Organization $organization): bool
    {
        return $organization->owner_user_id === $user->id;
    }

    public function forceDelete(User $user, Organization $organization): bool
    {
        return $organization->owner_user_id === $user->id;
    }
}
