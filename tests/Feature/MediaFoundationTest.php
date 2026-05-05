<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Organization;
use App\Models\Site;
use App\Models\User;
use App\Modules\Core\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_media_asset_metadata(): void
    {
        $uploader = User::factory()->create();
        $organization = Organization::factory()->create();
        $company = Company::factory()->create(['organization_id' => $organization->id]);
        $site = Site::factory()->forCompany($company)->create();

        $asset = app(MediaService::class)->createAsset([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'company_id' => $company->id,
            'uploaded_by' => $uploader->id,
            'disk' => 'public',
            'path' => 'organizations/'.$organization->id.'/media/logo.png',
            'relative_path' => 'media/logo.png',
            'filename' => 'logo.png',
            'original_filename' => 'logo-original.png',
            'mime_type' => 'image/png',
            'extension' => 'png',
            'size_bytes' => 102400,
            'width' => 1200,
            'height' => 630,
            'visibility' => 'organization_only',
            'alt_text' => 'Organization logo',
            'caption' => 'Logo for profile',
            'checksum' => 'abc123checksum',
            'metadata' => ['source' => 'test'],
        ]);

        $this->assertDatabaseHas('media_assets', [
            'id' => $asset->id,
            'organization_id' => $organization->id,
            'relative_path' => 'media/logo.png',
            'visibility' => 'organization_only',
        ]);
    }

    public function test_can_attach_media_usage_polymorphically(): void
    {
        $uploader = User::factory()->create();
        $organization = Organization::factory()->create();
        $company = Company::factory()->create(['organization_id' => $organization->id]);
        $site = Site::factory()->forCompany($company)->create();

        $asset = app(MediaService::class)->createAsset([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'company_id' => $company->id,
            'uploaded_by' => $uploader->id,
            'disk' => 'public',
            'path' => 'organizations/'.$organization->id.'/media/hero.jpg',
            'relative_path' => 'media/hero.jpg',
            'filename' => 'hero.jpg',
            'original_filename' => 'hero-original.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size_bytes' => 204800,
            'visibility' => 'public',
        ]);

        $usage = app(MediaService::class)->attachUsage($asset, $site, 'hero_image');

        $this->assertDatabaseHas('media_usages', [
            'id' => $usage->id,
            'media_asset_id' => $asset->id,
            'usable_type' => $site->getMorphClass(),
            'usable_id' => $site->id,
            'field_name' => 'hero_image',
        ]);
    }
}
