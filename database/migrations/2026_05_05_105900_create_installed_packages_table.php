<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installed_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('status', 30)->default('active');
            $table->foreignId('installed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('installed_at')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'site_id', 'package_id']);
            $table->index('status');
            $table->index('installed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installed_packages');
    }
};
