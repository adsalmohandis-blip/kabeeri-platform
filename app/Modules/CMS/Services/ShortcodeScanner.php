<?php

namespace App\Modules\CMS\Services;

use App\Models\ImportRecord;

class ShortcodeScanner
{
    protected array $knownSafe = ['gallery', 'caption', 'audio', 'video'];

    protected array $knownConvertible = ['contact-form-7', 'button', 'columns'];

    protected array $risky = ['embed', 'iframe', 'script', 'php'];

    /**
     * @return array{shortcodes: array<int, array<string, string>>, builders: array<int, string>, warnings: array<int, array<string, mixed>>}
     */
    public function scan(string $content): array
    {
        preg_match_all('/\[(\/?)([a-zA-Z0-9_-]+)(?:\s[^\]]*)?\]/', $content, $matches);

        $shortcodes = [];
        $warnings = [];

        foreach ($matches[2] as $name) {
            $name = strtolower((string) $name);
            $classification = $this->classify($name);
            $shortcodes[] = ['name' => $name, 'classification' => $classification];

            if (in_array($classification, ['unknown', 'risky'], true)) {
                $warnings[] = [
                    'type' => 'shortcode',
                    'name' => $name,
                    'classification' => $classification,
                    'message' => 'Unsupported shortcode detected.',
                ];
            }
        }

        $builders = $this->detectBuilders($content);

        foreach ($builders as $builder) {
            $warnings[] = [
                'type' => 'builder',
                'name' => $builder,
                'classification' => $builder === 'gutenberg' ? 'known_safe' : 'known_convertible',
                'message' => 'Page builder marker detected.',
            ];
        }

        return [
            'shortcodes' => array_values(array_unique($shortcodes, SORT_REGULAR)),
            'builders' => $builders,
            'warnings' => $warnings,
        ];
    }

    public function scanImportRecord(ImportRecord $record, string $content): ImportRecord
    {
        $scan = $this->scan($content);
        $existing = is_array($record->warnings) ? $record->warnings : [];

        $record->forceFill([
            'warnings' => array_merge($existing, $scan['warnings']),
            'metadata' => array_merge(is_array($record->metadata) ? $record->metadata : [], [
                'shortcode_scan' => [
                    'shortcodes' => $scan['shortcodes'],
                    'builders' => $scan['builders'],
                ],
            ]),
        ])->save();

        return $record->refresh();
    }

    protected function classify(string $name): string
    {
        if (in_array($name, $this->knownSafe, true)) {
            return 'known_safe';
        }

        if (in_array($name, $this->knownConvertible, true)) {
            return 'known_convertible';
        }

        if (in_array($name, $this->risky, true)) {
            return 'risky';
        }

        return 'unknown';
    }

    /**
     * @return array<int, string>
     */
    protected function detectBuilders(string $content): array
    {
        $builders = [];

        if (str_contains($content, 'elementor-') || str_contains($content, 'data-elementor-type')) {
            $builders[] = 'elementor';
        }

        if (str_contains($content, '<!-- wp:')) {
            $builders[] = 'gutenberg';
        }

        if (str_contains($content, '[vc_row') || str_contains($content, 'wpb_')) {
            $builders[] = 'wpbakery';
        }

        return array_values(array_unique($builders));
    }
}
