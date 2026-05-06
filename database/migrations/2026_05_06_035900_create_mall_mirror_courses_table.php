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
        Schema::create('mall_mirror_courses', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->foreignId('mall_publication_consent_id')->nullable()->constrained('mall_publication_consents')->nullOnDelete();
            $table->string('course_name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('training_type', 80)->nullable();
            $table->string('delivery_mode', 80)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->json('schedule')->nullable();
            $table->json('instructor_info')->nullable();
            $table->json('images')->nullable();
            $table->string('mirror_status', 40)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('last_refreshed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'mirror_status']);
            $table->index('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mall_mirror_courses');
    }
};
