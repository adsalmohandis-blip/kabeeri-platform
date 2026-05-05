<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_app_recipes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
            $table->string('project_type');
            $table->string('app_type');
            $table->json('required_packages')->nullable();
            $table->json('recommended_packages')->nullable();
            $table->json('optional_packages')->nullable();
            $table->string('demo_content_ref')->nullable();
            $table->json('setup_steps')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('theme_id');
            $table->index('project_type');
            $table->index('app_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theme_app_recipes');
    }
};
