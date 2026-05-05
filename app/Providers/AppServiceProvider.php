<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\FeatureFlag;
use App\Models\FeatureFlagOverride;
use App\Models\MediaAsset;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Site;
use App\Models\Taxonomy;
use App\Policies\ActivityLogPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\ContentEntryPolicy;
use App\Policies\ContentTypePolicy;
use App\Policies\FeatureFlagOverridePolicy;
use App\Policies\FeatureFlagPolicy;
use App\Policies\MediaAssetPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\SettingPolicy;
use App\Policies\SitePolicy;
use App\Policies\TaxonomyPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(ActivityLog::class, ActivityLogPolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(ContentEntry::class, ContentEntryPolicy::class);
        Gate::policy(ContentType::class, ContentTypePolicy::class);
        Gate::policy(FeatureFlag::class, FeatureFlagPolicy::class);
        Gate::policy(FeatureFlagOverride::class, FeatureFlagOverridePolicy::class);
        Gate::policy(MediaAsset::class, MediaAssetPolicy::class);
        Gate::policy(Organization::class, OrganizationPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(Site::class, SitePolicy::class);
        Gate::policy(Taxonomy::class, TaxonomyPolicy::class);
    }
}
