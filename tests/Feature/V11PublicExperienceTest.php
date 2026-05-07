<?php

namespace Tests\Feature;

use App\Support\Ui\V11PublicExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class V11PublicExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_v11_public_config_defines_pages_audiences_and_pricing(): void
    {
        $config = config('kabeeri_public');

        $this->assertSame('V11', $config['version']);
        $this->assertCount(14, $config['pages']);
        $this->assertGreaterThanOrEqual(5, count($config['audiences']));
        $this->assertGreaterThanOrEqual(6, count($config['story_layers']));
        $this->assertGreaterThanOrEqual(6, count($config['pricing_layers']));
        $this->assertSame('kabeeri-public-inquiries', $config['contact']['organization_slug']);
    }

    public function test_v11_public_routes_are_registered(): void
    {
        foreach (config('kabeeri_public.pages') as $page) {
            $this->assertTrue(Route::has($page['route']), "Missing V11 public route [{$page['route']}].");
        }

        $this->assertTrue(Route::has('public.contact.store'));
    }

    public function test_v11_public_pages_render(): void
    {
        foreach (config('kabeeri_public.pages') as $page) {
            $this->get(route($page['route']))
                ->assertOk()
                ->assertSee(__('kabeeri.brand.name').' Public Bridge')
                ->assertSee($page['label']);
        }
    }

    public function test_v11_public_pages_explain_core_audience_paths(): void
    {
        $this->get(route('public.audiences'))
            ->assertOk()
            ->assertSee('Audience Selector')
            ->assertSee('Business Owner')
            ->assertSee('Enterprise Buyer');

        $this->get(route('public.wordpress'))
            ->assertOk()
            ->assertSee('WordPress Alternative');

        $this->get(route('public.contact'))
            ->assertOk()
            ->assertSee('Contact Sales');
    }

    public function test_contact_sales_flow_creates_crm_lead(): void
    {
        $this->post(route('public.contact.store'), [
            'audience' => 'business',
            'plan_interest' => 'business',
            'company_name' => 'Acme Services',
            'name' => 'Mona',
            'email' => 'mona@example.com',
            'phone' => '+201000000000',
            'message' => 'Need a demo for a service business.',
        ])->assertRedirect(route('public.contact'));

        $this->assertDatabaseHas('organizations', [
            'slug' => 'kabeeri-public-inquiries',
        ]);

        $this->assertDatabaseHas('leads', [
            'email' => 'mona@example.com',
            'source' => 'public_v11_inquiry',
            'status' => 'new',
            'company_name' => 'Acme Services',
        ]);
    }

    public function test_v11_release_candidate_report_is_ready_after_tracker_sync(): void
    {
        $report = V11PublicExperience::validationReport();

        $this->assertSame([], $report['missing_routes']);
        $this->assertTrue($report['v9_ready']);
        $this->assertTrue($report['v10_ready']);
        $this->assertTrue($report['docs']['public_ux']);
        $this->assertTrue($report['docs']['release_report']);
        $this->assertTrue(V11PublicExperience::isReleaseCandidateReady());
    }
}
