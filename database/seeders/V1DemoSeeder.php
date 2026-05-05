<?php

namespace Database\Seeders;

use App\Models\BusinessProfile;
use App\Models\Company;
use App\Models\ContentEntry;
use App\Models\ContentType;
use App\Models\MediaAsset;
use App\Models\Organization;
use App\Models\OrganizationMembership;
use App\Models\Site;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Database\Seeder;

class V1DemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@kabeeri.local'],
            [
                'name' => 'KABEERI Platform Admin',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        $organization = Organization::query()->updateOrCreate(
            ['slug' => 'kabeeri-demo-org'],
            [
                'name' => 'KABEERI Demo Organization',
                'owner_user_id' => $admin->id,
                'account_type' => 'business',
                'status' => 'active',
                'locale' => 'ar',
                'timezone' => 'Africa/Cairo',
                'metadata' => ['seeded' => true, 'source' => 'v1_demo'],
            ],
        );

        OrganizationMembership::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'user_id' => $admin->id,
            ],
            [
                'membership_type' => 'owner',
                'status' => 'active',
                'accepted_at' => now(),
                'visibility' => 'organization_only',
                'metadata' => ['seeded' => true, 'source' => 'v1_demo'],
            ],
        );

        $company = Company::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'slug' => 'kabeeri-demo-company',
            ],
            [
                'trade_name' => 'KABEERI Demo Company',
                'legal_name' => 'KABEERI Demo Company LLC',
                'city' => 'Cairo',
                'status' => 'active',
                'verification_status' => 'not_submitted',
                'metadata' => ['seeded' => true, 'source' => 'v1_demo'],
            ],
        );

        $theme = Theme::query()->where('slug', 'kabeeri-starter')->first();

        $site = Site::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'slug' => 'kabeeri-demo-app',
            ],
            [
                'company_id' => $company->id,
                'name' => 'KABEERI Demo App',
                'site_type' => 'website',
                'status' => 'active',
                'language' => 'ar',
                'timezone' => 'Africa/Cairo',
                'theme_id' => $theme?->id,
                'created_by' => $admin->id,
                'settings' => ['app_label' => 'App'],
                'metadata' => ['seeded' => true, 'source' => 'v1_demo'],
            ],
        );

        $pageType = ContentType::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'slug' => 'page',
            ],
            [
                'name' => 'Page',
                'description' => 'Demo page content type',
                'fields' => [
                    ['key' => 'title', 'type' => 'string'],
                    ['key' => 'body', 'type' => 'rich_text'],
                ],
                'status' => 'active',
            ],
        );

        ContentType::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'slug' => 'post',
            ],
            [
                'name' => 'Post',
                'description' => 'Demo post content type',
                'fields' => [
                    ['key' => 'title', 'type' => 'string'],
                    ['key' => 'excerpt', 'type' => 'text'],
                    ['key' => 'body', 'type' => 'rich_text'],
                ],
                'status' => 'active',
            ],
        );

        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'excerpt' => 'Welcome to the KABEERI demo app.',
                'body' => 'This is the Home page of the KABEERI V1 demo.',
            ],
            [
                'title' => 'About',
                'slug' => 'about',
                'excerpt' => 'Know more about our demo organization.',
                'body' => 'This is the About page for the KABEERI V1 demo.',
            ],
            [
                'title' => 'Services',
                'slug' => 'services',
                'excerpt' => 'A quick overview of demo services.',
                'body' => 'This is the Services page for the KABEERI V1 demo.',
            ],
            [
                'title' => 'Contact',
                'slug' => 'contact',
                'excerpt' => 'How to reach the demo organization.',
                'body' => 'This is the Contact page for the KABEERI V1 demo.',
            ],
        ];

        foreach ($pages as $page) {
            ContentEntry::query()->updateOrCreate(
                [
                    'site_id' => $site->id,
                    'slug' => $page['slug'],
                ],
                [
                    'organization_id' => $organization->id,
                    'content_type_id' => $pageType->id,
                    'author_user_id' => $admin->id,
                    'title' => $page['title'],
                    'excerpt' => $page['excerpt'],
                    'body' => $page['body'],
                    'status' => 'published',
                    'visibility' => 'public',
                    'published_at' => now(),
                    'metadata' => ['seeded' => true, 'source' => 'v1_demo'],
                ],
            );
        }

        $media = MediaAsset::query()->where([
            'organization_id' => $organization->id,
            'relative_path' => 'demo/media/office-placeholder.jpg',
        ])->first();

        if (! $media) {
            $media = MediaAsset::query()->create([
                'organization_id' => $organization->id,
                'site_id' => $site->id,
                'company_id' => $company->id,
                'uploaded_by' => $admin->id,
                'disk' => 'public',
                'path' => 'demo/media/office-placeholder.jpg',
                'relative_path' => 'demo/media/office-placeholder.jpg',
                'filename' => 'office-placeholder.jpg',
                'original_filename' => 'office-placeholder.jpg',
                'mime_type' => 'image/jpeg',
                'extension' => 'jpg',
                'size_bytes' => 1024,
                'width' => 1200,
                'height' => 630,
                'visibility' => 'public',
                'alt_text' => 'Demo office placeholder image',
                'caption' => 'Placeholder media for V1 demo',
                'checksum' => 'v1-demo-media-placeholder',
                'metadata' => ['seeded' => true, 'source' => 'v1_demo'],
            ]);
        }

        BusinessProfile::query()->updateOrCreate(
            [
                'organization_id' => $organization->id,
                'slug' => 'kabeeri-demo-business',
            ],
            [
                'company_id' => $company->id,
                'site_id' => $site->id,
                'display_name' => 'KABEERI Demo Business',
                'description' => 'Draft business profile for V1 demo scenario.',
                'public_email' => 'info@kabeeri.local',
                'public_phone' => '+20-100-000-0000',
                'website_url' => 'https://example.test',
                'logo_media_id' => $media->id,
                'cover_media_id' => $media->id,
                'visibility' => 'draft',
                'status' => 'draft',
                'metadata' => ['seeded' => true, 'source' => 'v1_demo'],
            ],
        );
    }
}
