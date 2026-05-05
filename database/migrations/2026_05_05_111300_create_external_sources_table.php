<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_sources', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->cascadeOnDelete();
            $table->string('source_type');
            $table->string('source_name');
            $table->string('source_url')->nullable();
            $table->string('connection_type')->default('manual');
            $table->string('status', 30)->default('draft');
            $table->timestamp('last_sync_at')->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('company_id');
            $table->index('site_id');
            $table->index('source_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_sources');
    }
};
