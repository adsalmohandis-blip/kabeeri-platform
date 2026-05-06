<?php

namespace App\Modules\Mall\Services;

use App\Models\MallSyncEvent;
use App\Models\MallSyncSource;
use Illuminate\Validation\ValidationException;

class WordPressWooCommerceListingSyncPreview
{
    private const ALLOWED_TYPES = ['wordpress', 'woocommerce'];

    /**
     * @var list<string>
     */
    private const LISTING_TYPES = ['business', 'product', 'service', 'course', 'talent', 'travel'];

    /**
     * @param  array<string, mixed>|list<array<string, mixed>>  $payload
     * @return array{rows: list<array<string, mixed>>, warnings: list<string>}
     */
    public function parse(array $payload): array
    {
        $items = $payload['items'] ?? $payload;
        $warnings = [];
        $rows = [];

        if (! is_array($items)) {
            return ['rows' => [], 'warnings' => ['Payload items must be an array.']];
        }

        foreach (array_values($items) as $index => $row) {
            if (! is_array($row)) {
                $warnings[] = 'Row '.($index + 1).' must be an object.';

                continue;
            }

            $title = $row['title'] ?? $row['name'] ?? null;
            $type = $row['type'] ?? 'business';

            if (blank($title)) {
                $warnings[] = 'Row '.($index + 1).' is missing a title or name.';

                continue;
            }

            if (blank($row['slug'] ?? null)) {
                $warnings[] = 'Row '.($index + 1).' is missing a slug.';

                continue;
            }

            if (! is_string($type) || ! in_array($type, self::LISTING_TYPES, true)) {
                $warnings[] = 'Row '.($index + 1).' has an unsupported listing type.';

                continue;
            }

            $rows[] = $row;
        }

        return ['rows' => $rows, 'warnings' => $warnings];
    }

    /**
     * @param  array<string, mixed>|list<array<string, mixed>>  $payload
     */
    public function preview(MallSyncSource $source, array $payload): MallSyncEvent
    {
        if (! in_array($source->source_type, self::ALLOWED_TYPES, true)) {
            throw ValidationException::withMessages([
                'source_type' => 'Only WordPress and WooCommerce sources can use this preview processor.',
            ]);
        }

        $parsed = $this->parse($payload);

        $event = app(MallSyncEventService::class)->start($source, 'preview', [
            'processor' => 'wordpress_woocommerce_listing_preview',
            'source_type' => $source->source_type,
            'sync_scope' => $source->sync_scope,
        ]);

        return app(MallSyncEventService::class)->complete(
            $event,
            count($parsed['rows']) + count($parsed['warnings']),
            count($parsed['rows']),
            $parsed['warnings'],
        );
    }
}
