<?php

namespace App\Modules\Mall\Services;

use App\Models\MallSyncEvent;
use App\Models\MallSyncSource;

class CsvFeedPublicSyncProcessor
{
    /**
     * @return array{rows: list<array<string, string>>, warnings: list<string>}
     */
    public function parse(string $csv): array
    {
        $lines = array_values(array_filter(
            preg_split('/\r\n|\r|\n/', trim($csv)) ?: [],
            fn (string $line): bool => trim($line) !== '',
        ));

        if ($lines === []) {
            return ['rows' => [], 'warnings' => ['CSV feed is empty.']];
        }

        $headers = str_getcsv(array_shift($lines));
        $rows = [];
        $warnings = [];

        foreach ($lines as $index => $line) {
            $values = str_getcsv($line);

            if (count($values) !== count($headers)) {
                $warnings[] = 'Row '.($index + 2).' column count does not match header.';

                continue;
            }

            $row = array_combine($headers, $values);

            if (! is_array($row)) {
                $warnings[] = 'Row '.($index + 2).' could not be parsed.';

                continue;
            }

            $rows[] = $row;
        }

        return ['rows' => $rows, 'warnings' => $warnings];
    }

    public function preview(MallSyncSource $source, string $csv): MallSyncEvent
    {
        $parsed = $this->parse($csv);
        $event = app(MallSyncEventService::class)->start($source, 'preview', [
            'processor' => 'csv_feed_public_sync',
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
