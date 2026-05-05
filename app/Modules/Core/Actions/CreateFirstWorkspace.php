<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Services\OnboardingService;

class CreateFirstWorkspace
{
    public function __construct(
        protected OnboardingService $onboardingService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(User $user, array $data): array
    {
        return $this->onboardingService->createFirstWorkspace($user, $data);
    }
}
