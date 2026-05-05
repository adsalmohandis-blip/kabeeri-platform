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
        Schema::create('feature_flag_overrides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('feature_flag_id')->constrained('feature_flags')->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('scope_type', 40)->nullable();
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->boolean('value');
            $table->timestamps();

            $table->index('organization_id');
            $table->index(['scope_type', 'scope_id']);
            $table->unique(['feature_flag_id', 'organization_id', 'scope_type', 'scope_id'], 'feature_flag_overrides_unique_scope');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_flag_overrides');
    }
};
