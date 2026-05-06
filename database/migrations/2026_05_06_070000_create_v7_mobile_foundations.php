<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_app_configs', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->string('app_key');
            $table->string('name');
            $table->string('platform', 40)->default('universal');
            $table->string('status', 40)->default('draft');
            $table->string('min_supported_version', 40)->nullable();
            $table->string('current_version', 40)->nullable();
            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'app_key']);
            $table->index(['status', 'platform']);
        });

        Schema::create('mobile_theme_profiles', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('mobile_app_config_id')->constrained('mobile_app_configs')->cascadeOnDelete();
            $table->string('name');
            $table->string('status', 40)->default('draft');
            $table->json('colors')->nullable();
            $table->json('typography')->nullable();
            $table->json('layout')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('mobile_api_manifests', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('mobile_app_config_id')->constrained('mobile_app_configs')->cascadeOnDelete();
            $table->string('version', 40)->default('v1');
            $table->string('status', 40)->default('draft');
            $table->json('endpoints')->nullable();
            $table->json('capabilities')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['mobile_app_config_id', 'version']);
        });

        Schema::create('mobile_devices', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('mobile_app_config_id')->nullable()->constrained('mobile_app_configs')->nullOnDelete();
            $table->string('device_uuid');
            $table->string('platform', 40)->default('unknown');
            $table->string('app_version', 40)->nullable();
            $table->string('status', 40)->default('active');
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['mobile_app_config_id', 'device_uuid']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('mobile_push_tokens', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('mobile_device_id')->constrained('mobile_devices')->cascadeOnDelete();
            $table->string('provider', 40)->default('fcm');
            $table->string('token_hash');
            $table->string('status', 40)->default('active');
            $table->timestamp('last_used_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'token_hash']);
        });

        Schema::create('mobile_auth_tokens', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mobile_device_id')->nullable()->constrained('mobile_devices')->nullOnDelete();
            $table->string('token_hash')->unique();
            $table->string('status', 40)->default('active');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('abilities')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_auth_tokens');
        Schema::dropIfExists('mobile_push_tokens');
        Schema::dropIfExists('mobile_devices');
        Schema::dropIfExists('mobile_api_manifests');
        Schema::dropIfExists('mobile_theme_profiles');
        Schema::dropIfExists('mobile_app_configs');
    }
};
