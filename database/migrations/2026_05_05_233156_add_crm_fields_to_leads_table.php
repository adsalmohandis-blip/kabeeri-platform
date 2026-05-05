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
        Schema::table('leads', function (Blueprint $table): void {
            $table->foreignId('contact_id')->nullable()->after('form_submission_id')->constrained('contacts')->nullOnDelete();
            $table->string('title')->nullable()->after('contact_id');
            $table->string('company_name')->nullable()->after('title');
            $table->string('priority', 20)->default('normal')->after('status');
            $table->decimal('expected_value', 12, 2)->nullable()->after('priority');
            $table->string('currency_code', 3)->nullable()->after('expected_value');
            $table->timestamp('qualified_at')->nullable()->after('assigned_to');
            $table->timestamp('converted_at')->nullable()->after('qualified_at');
            $table->timestamp('lost_at')->nullable()->after('converted_at');
            $table->string('lost_reason')->nullable()->after('lost_at');

            $table->index('contact_id');
            $table->index('priority');
            $table->index('converted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('contact_id');
            $table->dropColumn([
                'title',
                'company_name',
                'priority',
                'expected_value',
                'currency_code',
                'qualified_at',
                'converted_at',
                'lost_at',
                'lost_reason',
            ]);
        });
    }
};
