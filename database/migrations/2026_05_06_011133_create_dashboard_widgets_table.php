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
        Schema::create('dashboard_widgets', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('report_definition_id')->nullable()->constrained('report_definitions')->nullOnDelete();
            $table->string('key');
            $table->string('title');
            $table->string('widget_type', 50)->default('metric');
            $table->string('status', 30)->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'key']);
            $table->index('report_definition_id');
            $table->index('widget_type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widgets');
    }
};
