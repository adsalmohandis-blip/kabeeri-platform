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
        Schema::create('agency_partner_profiles', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('display_name');
            $table->string('slug');
            $table->string('agency_type', 60)->default('implementation_partner');
            $table->string('status', 40)->default('draft');
            $table->string('accreditation_status', 40)->default('not_submitted');
            $table->string('accreditation_level', 40)->nullable();
            $table->json('service_categories')->nullable();
            $table->json('regions')->nullable();
            $table->json('languages')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accredited_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
            $table->index(['agency_type', 'status']);
            $table->index(['accreditation_status', 'accreditation_level'], 'agency_partner_accreditation_status_level_index');
            $table->index(['organization_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_partner_profiles');
    }
};
