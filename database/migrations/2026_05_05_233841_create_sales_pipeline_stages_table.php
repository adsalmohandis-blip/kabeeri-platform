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
        Schema::create('sales_pipeline_stages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_pipeline_id')->constrained('sales_pipelines')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedSmallInteger('probability')->default(0);
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);
            $table->string('status', 30)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['sales_pipeline_id', 'slug']);
            $table->index(['sales_pipeline_id', 'sort_order']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_pipeline_stages');
    }
};
