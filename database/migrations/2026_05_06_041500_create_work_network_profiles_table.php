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
        Schema::create('work_network_profiles', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->string('display_name');
            $table->string('slug');
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->json('skills')->nullable();
            $table->json('links')->nullable();
            $table->string('visibility', 40)->default('private');
            $table->string('status', 40)->default('draft');
            $table->string('availability_status', 40)->default('not_listed');
            $table->timestamp('published_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'slug']);
            $table->index(['organization_id', 'status']);
            $table->index(['visibility', 'availability_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_network_profiles');
    }
};
