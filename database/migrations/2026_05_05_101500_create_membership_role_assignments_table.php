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
        Schema::create('membership_role_assignments', function (Blueprint $table): void {
            $table->id();
            $table->string('membership_type', 60);
            $table->unsignedBigInteger('membership_id');
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->string('scope_type', 40)->nullable();
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['membership_type', 'membership_id']);
            $table->index(['scope_type', 'scope_id']);
            $table->unique(['membership_type', 'membership_id', 'role_id', 'scope_type', 'scope_id'], 'membership_role_assignments_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_role_assignments');
    }
};
