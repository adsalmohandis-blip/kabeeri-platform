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
            $table->foreignId('lead_source_id')->nullable()->after('source')->constrained('lead_sources')->nullOnDelete();
            $table->json('score_breakdown')->nullable()->after('score');
            $table->timestamp('scored_at')->nullable()->after('score_breakdown');

            $table->index('lead_source_id');
            $table->index('scored_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('lead_source_id');
            $table->dropColumn(['score_breakdown', 'scored_at']);
        });
    }
};
