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
        Schema::create('companies', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('legal_name')->nullable();
            $table->string('trade_name');
            $table->string('slug');
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('city')->nullable();
            $table->string('legal_type')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();
            $table->string('status', 30)->default('draft');
            $table->string('verification_status', 30)->default('not_submitted');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('verification_expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->unique(['organization_id', 'slug']);
            $table->index('verification_status');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
