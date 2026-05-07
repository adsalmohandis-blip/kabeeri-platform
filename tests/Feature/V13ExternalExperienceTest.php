<?php

namespace Tests\Feature;

use App\Models\MallMirrorBusiness;
use App\Models\MallMirrorCourse;
use App\Models\MallMirrorProduct;
use App\Models\MallMirrorService;
use App\Models\MallMirrorTalent;
use App\Models\TravelTourismMallListing;
use App\Support\Ui\V13ExternalExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class V13ExternalExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_v13_external_config_defines_mall_customer_partner_and_network_contracts(): void
    {
        $config = config('kabeeri_external');

        $this->assertSame('V13', $config['version']);
        $this->assertGreaterThanOrEqual(21, count($config['pages']));
        $this->assertCount(6, $config['mall_sections']);
        $this->assertGreaterThanOrEqual(4, count($config['trust_badges']));
        $this->assertGreaterThanOrEqual(5, count($config['customer_steps']));
        $this->assertGreaterThanOrEqual(4, count($config['partner_paths']));
        $this->assertSame('kabeeri Mall is public discovery. kabeeri Marketplace is internal extensions. Do not mix Mall leads with package install flows.', $config['rules']['mall_vs_marketplace']);
    }

    public function test_v13_external_routes_and_admin_handoffs_are_registered(): void
    {
        foreach (config('kabeeri_external.pages') as $page) {
            $this->assertTrue(Route::has($page['route']), "Missing V13 route [{$page['route']}].");
        }

        foreach (config('kabeeri_external.mall_sections') as $section) {
            $this->assertTrue(Route::has($section['route']), "Missing V13 Mall section route [{$section['route']}].");
        }

        foreach (config('kabeeri_external.admin_routes') as $route) {
            $this->assertTrue(Route::has($route), "Missing V13 admin route [{$route}].");
        }
    }

    public function test_v13_external_pages_render(): void
    {
        foreach (config('kabeeri_external.pages') as $page) {
            $this->get(route($page['route']))
                ->assertOk()
                ->assertSee(__('kabeeri.brand.name').' V13')
                ->assertSee($page['label']);
        }
    }

    public function test_existing_mall_sections_render_v13_shell(): void
    {
        MallMirrorBusiness::factory()->create(['display_name' => 'V13 Business', 'slug' => 'v13-business', 'mirror_status' => 'published', 'published_at' => now()]);
        MallMirrorProduct::factory()->create(['product_name' => 'V13 Product', 'slug' => 'v13-product', 'mirror_status' => 'published', 'published_at' => now()]);
        MallMirrorService::factory()->create(['service_name' => 'V13 Service', 'slug' => 'v13-service', 'mirror_status' => 'published', 'published_at' => now()]);
        MallMirrorCourse::factory()->create(['course_name' => 'V13 Course', 'slug' => 'v13-course', 'mirror_status' => 'published', 'published_at' => now()]);
        MallMirrorTalent::factory()->create(['display_name' => 'V13 Talent', 'slug' => 'v13-talent', 'mirror_status' => 'published', 'published_at' => now()]);
        TravelTourismMallListing::factory()->create(['title' => 'V13 Travel', 'slug' => 'v13-travel', 'listing_status' => 'published', 'published_at' => now()]);

        foreach ([
            'mall.businesses.index' => 'V13 Business',
            'mall.products.index' => 'V13 Product',
            'mall.services.index' => 'V13 Service',
            'mall.courses.index' => 'V13 Course',
            'mall.talent.index' => 'V13 Talent',
            'mall.travel.index' => 'V13 Travel',
        ] as $route => $expected) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee(__('kabeeri.brand.name').' V13 Mall UX')
                ->assertSee($expected)
                ->assertSee('Mall is public discovery. Marketplace is internal extensions.');
        }
    }

    public function test_mall_search_filters_section_results_by_query(): void
    {
        MallMirrorProduct::factory()->create([
            'product_name' => 'Needle Product',
            'slug' => 'needle-product',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);
        MallMirrorProduct::factory()->create([
            'product_name' => 'Other Product',
            'slug' => 'other-product',
            'mirror_status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('mall.products.index', ['q' => 'Needle']))
            ->assertOk()
            ->assertSee('Needle Product')
            ->assertDontSee('Other Product');
    }

    public function test_trust_claim_partner_referral_network_and_legal_pages_explain_v13_flows(): void
    {
        $this->get(route('mall.trust'))
            ->assertOk()
            ->assertSee('Mall Verification Trust Badge and Moderation UX')
            ->assertSee('Published with consent');

        $this->get(route('mall.claim-report'))
            ->assertOk()
            ->assertSee('Mall Listing Claim Submit and Report Flow')
            ->assertSee('Prove relationship');

        $this->get(route('partners.referrals'))
            ->assertOk()
            ->assertSee('Marketer Referral Dashboard and Commission Placeholder UI')
            ->assertSee('Commission');

        $this->get(route('network.talent-path'))
            ->assertOk()
            ->assertSee('Public Talent Console')
            ->assertSee('Professional profile');

        $this->get(route('partners.legal-verification'))
            ->assertOk()
            ->assertSee('Legal Partner Verification Journey UI')
            ->assertSee('Public trust record');
    }

    public function test_v13_release_candidate_report_is_ready_after_tracker_sync(): void
    {
        $report = V13ExternalExperience::validationReport();

        $this->assertSame([], $report['missing_routes']);
        $this->assertSame([], $report['missing_mall_routes']);
        $this->assertSame([], $report['missing_admin_routes']);
        $this->assertSame([], $report['missing_database_tables']);
        $this->assertTrue($report['v9_ready']);
        $this->assertTrue($report['v10_ready']);
        $this->assertTrue($report['v11_ready']);
        $this->assertTrue($report['v12_ready']);
        $this->assertTrue($report['docs']['external_ux']);
        $this->assertTrue($report['docs']['release_report']);
        $this->assertTrue(V13ExternalExperience::isReleaseCandidateReady());
    }
}
