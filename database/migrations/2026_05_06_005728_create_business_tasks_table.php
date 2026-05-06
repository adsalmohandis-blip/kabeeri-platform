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
        Schema::create('business_tasks', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('business_project_id')->nullable()->constrained('business_projects')->nullOnDelete();
            $table->foreignId('assigned_employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 30)->default('todo');
            $table->string('priority', 20)->default('normal');
            $table->date('due_on')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('business_project_id');
            $table->index('assigned_employee_profile_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_tasks');
    }
};
