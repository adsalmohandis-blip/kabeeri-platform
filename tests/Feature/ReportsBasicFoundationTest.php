<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Modules\BusinessOperations\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsBasicFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_definition_and_snapshot_can_be_created(): void
    {
        $organization = Organization::factory()->create();
        $service = app(ReportService::class);

        $definition = $service->createDefinition($organization, [
            'key' => 'invoice-summary',
            'name' => 'Invoice Summary',
            'report_type' => 'table',
            'columns' => ['invoice_number', 'total'],
        ]);
        $snapshot = $service->snapshot($definition, ['month' => '2026-05'], [
            'rows' => [['invoice_number' => 'INV-00001', 'total' => 100]],
        ]);

        $this->assertNotNull($definition->ulid);
        $this->assertNotNull($snapshot->ulid);
        $this->assertTrue($snapshot->definition->is($definition));
        $this->assertSame('2026-05', $snapshot->parameters['month']);
        $this->assertSame(100, $snapshot->data['rows'][0]['total']);
    }
}
