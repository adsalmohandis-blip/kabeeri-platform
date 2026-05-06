<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('helpdesk_tickets', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('ticketable');
            $table->string('ticket_number');
            $table->string('subject');
            $table->string('channel', 40)->default('manual');
            $table->string('status', 40)->default('open');
            $table->string('priority', 40)->default('normal');
            $table->timestamp('first_response_due_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'ticket_number']);
            $table->index(['organization_id', 'status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('helpdesk_tickets');
    }
};
