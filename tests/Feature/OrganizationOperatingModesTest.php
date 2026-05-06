<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\OrganizationOperatingMode;
use App\Modules\Core\Services\OrganizationOperatingModeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrganizationOperatingModesTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_can_define_operating_modes(): void
    {
        $organization = Organization::factory()->create();

        $mode = app(OrganizationOperatingModeService::class)->createMode($organization, 'marketplace', 'Marketplace', [
            'settings' => ['public_mall' => true],
        ]);

        $this->assertSame($organization->id, $mode->organization_id);
        $this->assertSame('marketplace', $mode->mode_key);
        $this->assertFalse($mode->is_active);
        $this->assertSame(['public_mall' => true], $mode->settings);
        $this->assertTrue($organization->operatingModes()->where('mode_key', 'marketplace')->exists());
    }

    public function test_only_one_operating_mode_is_active_per_organization(): void
    {
        $organization = Organization::factory()->create();
        $service = app(OrganizationOperatingModeService::class);

        $standard = $service->createMode($organization, 'standard', 'Standard');
        $marketplace = $service->createMode($organization, 'marketplace', 'Marketplace');

        $service->activate($standard);
        $active = $service->activate($marketplace);

        $this->assertTrue($active->is_active);
        $this->assertSame('marketplace', $service->activeFor($organization)?->mode_key);
        $this->assertFalse($standard->refresh()->is_active);
        $this->assertSame('inactive', $standard->status);
    }

    public function test_disabled_operating_mode_cannot_be_activated(): void
    {
        $mode = OrganizationOperatingMode::factory()->create(['status' => 'disabled']);

        $this->expectException(ValidationException::class);

        app(OrganizationOperatingModeService::class)->activate($mode);
    }
}
