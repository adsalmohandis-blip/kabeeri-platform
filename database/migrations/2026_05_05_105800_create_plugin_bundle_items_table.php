<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_bundle_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('plugin_bundle_id')->constrained('plugin_bundles')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('requirement_level', 30)->default('recommended');
            $table->unsignedInteger('sort_order')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['plugin_bundle_id', 'package_id']);
            $table->index('requirement_level');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_bundle_items');
    }
};
