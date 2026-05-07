<?php

namespace Tests\Feature;

use App\Modules\Core\Services\PackageManifestValidator;
use Tests\TestCase;

class PackageManifestValidatorTest extends TestCase
{
    public function test_valid_manifest_passes(): void
    {
        $result = app(PackageManifestValidator::class)->validate([
            'key' => 'kabeeri.forms',
            'name' => 'kabeeri Forms',
            'version' => '1.0.0',
            'package_type' => 'module',
            'permissions' => ['form.view'],
            'dependencies' => [],
            'compatibility' => ['v2' => true],
        ]);

        $this->assertTrue($result['valid']);
        $this->assertSame([], $result['errors']);
    }

    public function test_invalid_manifest_returns_clear_errors(): void
    {
        $result = app(PackageManifestValidator::class)->validate([
            'key' => 'Bad Key',
            'name' => '',
            'permissions' => 'form.view',
        ]);

        $this->assertFalse($result['valid']);
        $this->assertArrayHasKey('key', $result['errors']);
        $this->assertArrayHasKey('name', $result['errors']);
        $this->assertArrayHasKey('version', $result['errors']);
        $this->assertArrayHasKey('package_type', $result['errors']);
        $this->assertArrayHasKey('permissions', $result['errors']);
    }
}
