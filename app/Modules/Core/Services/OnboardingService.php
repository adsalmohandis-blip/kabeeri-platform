<?php

namespace App\Modules\Core\Services;

use App\Models\Company;
use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Actions\CreateOrganizationWithOwnerMembership;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OnboardingService
{
    public function __construct(
        protected CreateOrganizationWithOwnerMembership $createOrganizationWithOwnerMembership,
        protected ActivityLogger $activityLogger,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *     organization: Organization,
     *     company: Company|null,
     *     site: Site,
     *     seeded_content_entry: ContentEntry|null
     * }
     */
    public function createFirstWorkspace(User $user, array $data): array
    {
        $organizationName = trim((string) ($data['organization_name'] ?? ''));
        $siteName = trim((string) ($data['site_name'] ?? ''));

        if ($organizationName === '') {
            throw new InvalidArgumentException('organization_name is required.');
        }

        if ($siteName === '') {
            throw new InvalidArgumentException('site_name is required.');
        }

        return DB::transaction(function () use ($user, $data, $organizationName, $siteName): array {
            $timezone = (string) ($data['timezone'] ?? 'Africa/Cairo');
            $locale = (string) ($data['locale'] ?? 'ar');

            $organization = ($this->createOrganizationWithOwnerMembership)(
                [
                    'name' => $organizationName,
                    'slug' => $this->generateUniqueSlug(Organization::class, $organizationName),
                    'account_type' => (string) ($data['organization_account_type'] ?? 'business'),
                    'status' => 'active',
                    'country_id' => $data['country_id'] ?? null,
                    'locale' => $locale,
                    'timezone' => $timezone,
                ],
                $user,
            );

            $this->activityLogger->log(
                action: 'onboarding.organization.created',
                organizationId: $organization->id,
                actorUserId: $user->id,
                description: 'Organization created during onboarding',
                subject: $organization,
            );

            $company = null;

            if ((bool) ($data['create_company_draft'] ?? false)) {
                $companyName = trim((string) ($data['company_name'] ?? $organizationName));

                $company = Company::query()->create([
                    'organization_id' => $organization->id,
                    'trade_name' => $companyName,
                    'slug' => $this->generateUniqueSlug(
                        Company::class,
                        $companyName,
                        ['organization_id' => $organization->id],
                    ),
                    'status' => 'draft',
                    'verification_status' => 'not_submitted',
                    'metadata' => ['source' => 'onboarding'],
                ]);

                $this->activityLogger->log(
                    action: 'onboarding.company_draft.created',
                    organizationId: $organization->id,
                    companyId: $company->id,
                    actorUserId: $user->id,
                    description: 'Draft company created during onboarding',
                    subject: $company,
                );
            }

            $site = Site::query()->create([
                'organization_id' => $organization->id,
                'company_id' => $company?->id,
                'name' => $siteName,
                'slug' => $this->generateUniqueSlug(
                    Site::class,
                    $siteName,
                    ['organization_id' => $organization->id],
                ),
                'site_type' => (string) ($data['site_type'] ?? 'website'),
                'status' => 'active',
                'language' => (string) ($data['language'] ?? 'ar'),
                'timezone' => $timezone,
                'created_by' => $user->id,
                'settings' => ['app_label' => 'App'],
                'metadata' => ['source' => 'onboarding'],
            ]);

            $this->activityLogger->log(
                action: 'onboarding.site.created',
                organizationId: $organization->id,
                companyId: $company?->id,
                siteId: $site->id,
                actorUserId: $user->id,
                description: 'First app/site created during onboarding',
                subject: $site,
            );

            $seededContentEntry = null;

            if ((bool) ($data['seed_basic_cms_page'] ?? false)) {
                $pageType = ContentType::query()->updateOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'site_id' => $site->id,
                        'slug' => 'page',
                    ],
                    [
                        'name' => 'Page',
                        'description' => 'Basic page content type',
                        'fields' => [
                            ['key' => 'title', 'type' => 'string'],
                            ['key' => 'body', 'type' => 'rich_text'],
                        ],
                        'status' => 'active',
                    ],
                );

                $seedPageTitle = trim((string) ($data['seed_page_title'] ?? 'Home'));
                $seedPageSlugSource = trim((string) ($data['seed_page_slug'] ?? 'home'));

                $seededContentEntry = ContentEntry::query()->create([
                    'organization_id' => $organization->id,
                    'site_id' => $site->id,
                    'content_type_id' => $pageType->id,
                    'author_user_id' => $user->id,
                    'title' => $seedPageTitle,
                    'slug' => $this->generateUniqueSlug(
                        ContentEntry::class,
                        $seedPageSlugSource,
                        ['site_id' => $site->id],
                    ),
                    'body' => (string) ($data['seed_page_body'] ?? 'Welcome to your new app.'),
                    'status' => 'draft',
                    'visibility' => 'public',
                    'metadata' => ['source' => 'onboarding'],
                ]);

                $this->activityLogger->log(
                    action: 'onboarding.cms_page.seeded',
                    organizationId: $organization->id,
                    companyId: $company?->id,
                    siteId: $site->id,
                    actorUserId: $user->id,
                    description: 'Basic CMS page seeded during onboarding',
                    subject: $seededContentEntry,
                );
            }

            $this->activityLogger->log(
                action: 'onboarding.workspace.created',
                organizationId: $organization->id,
                companyId: $company?->id,
                siteId: $site->id,
                actorUserId: $user->id,
                description: 'Initial workspace created',
                subject: $organization,
            );

            return [
                'organization' => $organization->refresh(),
                'company' => $company?->refresh(),
                'site' => $site->refresh(),
                'seeded_content_entry' => $seededContentEntry?->refresh(),
            ];
        });
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $constraints
     */
    protected function generateUniqueSlug(string $modelClass, string $source, array $constraints = []): string
    {
        $base = Str::slug($source);

        if ($base === '') {
            $base = 'item';
        }

        $slug = $base;
        $suffix = 2;

        while (
            $modelClass::query()
                ->where($constraints)
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
