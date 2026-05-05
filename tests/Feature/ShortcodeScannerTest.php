<?php

namespace Tests\Feature;

use App\Models\ImportRecord;
use App\Modules\CMS\Services\ShortcodeScanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortcodeScannerTest extends TestCase
{
    use RefreshDatabase;

    public function test_shortcodes_are_detected_and_classified(): void
    {
        $result = app(ShortcodeScanner::class)->scan(
            '[gallery ids="1,2"] [contact-form-7 id="1"] [iframe src="x"] [mystery]',
        );

        $this->assertContains(['name' => 'gallery', 'classification' => 'known_safe'], $result['shortcodes']);
        $this->assertContains(['name' => 'contact-form-7', 'classification' => 'known_convertible'], $result['shortcodes']);
        $this->assertContains(['name' => 'iframe', 'classification' => 'risky'], $result['shortcodes']);
        $this->assertContains(['name' => 'mystery', 'classification' => 'unknown'], $result['shortcodes']);
        $this->assertCount(2, $result['warnings']);
    }

    public function test_builder_markers_are_detected(): void
    {
        $result = app(ShortcodeScanner::class)->scan(
            '<div data-elementor-type="wp-page"></div><!-- wp:paragraph --><p>x</p>[vc_row]',
        );

        $this->assertSame(['elementor', 'gutenberg', 'wpbakery'], $result['builders']);
    }

    public function test_warnings_are_recorded_on_import_record(): void
    {
        $record = ImportRecord::factory()->create(['warnings' => null]);

        $updated = app(ShortcodeScanner::class)->scanImportRecord($record, '[unknown_code]');

        $this->assertSame('unknown_code', $updated->warnings[0]['name']);
        $this->assertSame('unknown', $updated->warnings[0]['classification']);
        $this->assertSame('unknown_code', $updated->metadata['shortcode_scan']['shortcodes'][0]['name']);
    }
}
