<?php

namespace Tests\Feature;

use App\Models\CsvImport;
use App\Models\ExternalSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvImportFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_csv_import_can_be_created(): void
    {
        $externalSource = ExternalSource::factory()->create(['source_type' => 'csv_feed']);

        $csvImport = CsvImport::factory()->create([
            'external_source_id' => $externalSource->id,
            'file_path' => 'storage/csv_imports/test.csv',
            'status' => 'pending',
        ]);

        $this->assertSame('pending', $csvImport->status);
        $this->assertSame('storage/csv_imports/test.csv', $csvImport->file_path);
        $this->assertInstanceOf(ExternalSource::class, $csvImport->externalSource);
    }
}
