<?php

namespace App\Modules\Rabet\Services;

use App\Models\AgencyPartnerProfile;
use App\Models\MarketplaceCatalogItem;
use App\Models\Organization;
use App\Models\Package;
use App\Models\PartnerCatalogShare;
use App\Models\PartnerStorefront;
use App\Models\Theme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PartnerStorefrontService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createDraft(Organization $organization, ?AgencyPartnerProfile $agency = null, array $attributes = []): PartnerStorefront
    {
        if ($agency !== null && (int) $agency->organization_id !== (int) $organization->id) {
            throw ValidationException::withMessages([
                'agency_partner_profile_id' => 'The agency partner does not belong to this organization.',
            ]);
        }

        $name = $attributes['name'] ?? ($agency === null ? 'Partner Storefront' : $agency->display_name.' Storefront');

        return PartnerStorefront::query()->create([
            ...$attributes,
            'organization_id' => $organization->id,
            'agency_partner_profile_id' => $agency?->id,
            'name' => $name,
            'slug' => $attributes['slug'] ?? Str::slug($name),
            'storefront_type' => $attributes['storefront_type'] ?? 'partner_catalog',
            'status' => 'draft',
            'visibility' => 'private',
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function shareDraft(PartnerStorefront $storefront, Model $catalogable, array $attributes = []): PartnerCatalogShare
    {
        $this->assertShareableCatalogable($catalogable);

        return PartnerCatalogShare::query()->create([
            ...$attributes,
            'organization_id' => $storefront->organization_id,
            'partner_storefront_id' => $storefront->id,
            'catalogable_type' => $catalogable->getMorphClass(),
            'catalogable_id' => $catalogable->getKey(),
            'share_type' => $attributes['share_type'] ?? 'catalog_item',
            'status' => 'draft',
            'visibility' => 'private',
        ]);
    }

    private function assertShareableCatalogable(Model $catalogable): void
    {
        if (! in_array($catalogable::class, [Package::class, Theme::class, MarketplaceCatalogItem::class], true)) {
            throw ValidationException::withMessages([
                'catalogable' => 'Partner storefronts can only share package, theme, or marketplace catalog records.',
            ]);
        }
    }
}
