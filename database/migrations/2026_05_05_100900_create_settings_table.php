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
        Schema::create('settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('scope_type', 40);
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('key');
            $table->json('value')->nullable();
            $table->string('value_type', 30)->default('string');
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            $table->unique(['scope_type', 'scope_id', 'key']);
            $table->index('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
