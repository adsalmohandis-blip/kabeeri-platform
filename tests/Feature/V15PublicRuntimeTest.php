<?php

namespace Tests\Feature;

use App\Support\Ui\V15PublicRuntime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V15PublicRuntimeTest extends TestCase
{
    use RefreshDatabase;

    public function test_v15_public_runtime_config_defines_next_workspace_and_manifest_contract(): void
    {
        $this->assertSame('V15', config('kabeeri_public_runtime.version'));
        $this->assertSame('apps/public-web', config('kabeeri_public_runtime.workspace.path'));
        $this->assertSame('public-web.v1', config('kabeeri_public_runtime.api.contract_version'));
        $this->assertSame('/api/public-web/manifest', config('kabeeri_public_runtime.api.manifest_uri'));
        $this->assertCount(6, config('kabeeri_public_runtime.routes'));
    }

    public function test_v15_manifest_endpoint_returns_public_runtime_contract(): void
    {
        $this->getJson('/api/public-web/manifest')
            ->assertOk()
            ->assertJsonPath('version', 'V15')
            ->assertJsonPath('contract', 'public-web.v1')
            ->assertJsonPath('runtime.path', 'apps/public-web')
            ->assertJsonPath('theme_manifest.default_direction', 'rtl')
            ->assertJsonPath('api.manifest_uri', '/api/public-web/manifest')
            ->assertJsonFragment(['key' => 'marketplace'])
            ->assertJsonFragment(['key' => 'mall']);
    }

    public function test_v15_validation_report_confirms_next_workspace_files_and_scripts(): void
    {
        $report = V15PublicRuntime::validationReport();

        $this->assertTrue($report['manifest_route_exists']);
        $this->assertTrue($report['workspace_exists']);
        $this->assertNotContains(false, $report['required_files']);
        $this->assertNotContains(false, $report['root_scripts']);
        $this->assertNotContains(false, $report['workspace_scripts']);
    }

    public function test_v15_route_registry_includes_public_web_api_contract(): void
    {
        $contracts = collect(config('kabeeri_ui.route_registry.api_contract_candidates'));
        $publicWeb = $contracts->firstWhere('key', 'public_web_manifest');

        $this->assertNotNull($publicWeb);
        $this->assertSame('public-web.manifest', $publicWeb['route']);
        $this->assertSame('/api/public-web/manifest', $publicWeb['uri']);
        $this->assertSame('next_public_runtime', $publicWeb['consumer']);
    }

    public function test_v15_release_candidate_ready_after_tracker_sync(): void
    {
        $this->assertTrue(V15PublicRuntime::isReleaseCandidateReady());
    }
}
