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
        Schema::table('employee_profiles', function (Blueprint $table): void {
            $table->foreignId('department_id')->nullable()->after('company_id')->constrained('departments')->nullOnDelete();
            $table->foreignId('position_id')->nullable()->after('department_id')->constrained('positions')->nullOnDelete();

            $table->index('department_id');
            $table->index('position_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('position_id');
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
