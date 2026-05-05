<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->foreignId('sales_pipeline_id')->nullable()->after('lead_source_id')->constrained('sales_pipelines')->nullOnDelete();
            $table->foreignId('sales_pipeline_stage_id')->nullable()->after('sales_pipeline_id')->constrained('sales_pipeline_stages')->nullOnDelete();
            $table->timestamp('stage_changed_at')->nullable()->after('scored_at');

            $table->index('sales_pipeline_id');
            $table->index('sales_pipeline_stage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('sales_pipeline_stage_id');
            $table->dropConstrainedForeignId('sales_pipeline_id');
            $table->dropColumn('stage_changed_at');
        });
    }
};
