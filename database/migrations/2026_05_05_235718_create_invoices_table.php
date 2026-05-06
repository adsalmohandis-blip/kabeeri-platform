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
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('site_id')->nullable()->constrained('sites')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();
            $table->string('invoice_number');
            $table->string('status', 30)->default('draft');
            $table->string('payment_status', 30)->default('unpaid');
            $table->string('currency_code', 3)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('paid_total', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'invoice_number']);
            $table->index('organization_id');
            $table->index('quotation_id');
            $table->index('status');
            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
