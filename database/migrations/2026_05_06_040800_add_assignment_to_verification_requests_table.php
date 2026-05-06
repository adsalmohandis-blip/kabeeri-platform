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
        Schema::table('verification_requests', function (Blueprint $table): void {
            $table->foreignId('assigned_to_user_id')->nullable()->after('reviewed_by')->constrained('users')->nullOnDelete();
            $table->string('assignment_status', 40)->default('unassigned')->after('assigned_to_user_id');
            $table->timestamp('assigned_at')->nullable()->after('assignment_status');
            $table->timestamp('due_at')->nullable()->after('assigned_at');

            $table->index(['assigned_to_user_id', 'assignment_status'], 'verification_requests_assignee_status_index');
            $table->index(['organization_id', 'assignment_status'], 'verification_requests_org_assignment_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_requests', function (Blueprint $table): void {
            $table->dropIndex('verification_requests_assignee_status_index');
            $table->dropIndex('verification_requests_org_assignment_status_index');
            $table->dropConstrainedForeignId('assigned_to_user_id');
            $table->dropColumn(['assignment_status', 'assigned_at', 'due_at']);
        });
    }
};
