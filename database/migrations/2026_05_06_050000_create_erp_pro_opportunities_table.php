<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_pro_opportunities', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('sales_pipeline_id')->nullable()->constrained('sales_pipelines')->nullOnDelete();
            $table->foreignId('sales_pipeline_stage_id')->nullable()->constrained('sales_pipeline_stages')->nullOnDelete();
            $table->string('name');
            $table->string('status', 40)->default('open');
            $table->string('priority', 40)->default('normal');
            $table->decimal('expected_value', 14, 2)->default(0);
            $table->string('currency_code', 3)->default('EGP');
            $table->unsignedTinyInteger('probability')->default(0);
            $table->timestamp('expected_close_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'expected_close_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erp_pro_opportunities');
    }
};
