<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->exists;
    }

    public function view(User $user, Company $company): bool
    {
        return $company->organization->owner_user_id === $user->id
            || $company->organization->memberships()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();
    }

    public function create(User $user): bool
    {
        return $user->exists;
    }

    public function update(User $user, Company $company): bool
    {
        return $company->organization->owner_user_id === $user->id;
    }

    public function delete(User $user, Company $company): bool
    {
        return $company->organization->owner_user_id === $user->id;
    }

    public function restore(User $user, Company $company): bool
    {
        return $company->organization->owner_user_id === $user->id;
    }

    public function forceDelete(User $user, Company $company): bool
    {
        return $company->organization->owner_user_id === $user->id;
    }
}
