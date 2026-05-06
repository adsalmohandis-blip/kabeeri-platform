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
        Schema::create('approval_requests', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('workflow_run_id')->nullable()->constrained('workflow_runs')->nullOnDelete();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->foreignId('requested_by_employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->foreignId('approver_employee_profile_id')->nullable()->constrained('employee_profiles')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('decision_notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('organization_id');
            $table->index('workflow_run_id');
            $table->index(['subject_type', 'subject_id']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_requests');
    }
};
