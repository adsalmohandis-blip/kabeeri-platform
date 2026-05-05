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
        Schema::table('quotations', function (Blueprint $table): void {
            $table->timestamp('issued_at')->nullable()->after('valid_until');
            $table->timestamp('accepted_at')->nullable()->after('issued_at');
            $table->timestamp('declined_at')->nullable()->after('accepted_at');
            $table->string('decline_reason')->nullable()->after('declined_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table): void {
            $table->dropColumn(['issued_at', 'accepted_at', 'declined_at', 'decline_reason']);
        });
    }
};
