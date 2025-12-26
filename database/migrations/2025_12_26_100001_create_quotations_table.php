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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 30);
            $table->unsignedTinyInteger('revision')->default(0);

            // Unique constraint on quotation_number + revision
            $table->unique(['quotation_number', 'revision']);
            $table->foreignId('contact_id')->constrained()->restrictOnDelete();

            $table->date('quotation_date');
            $table->date('valid_until');
            $table->string('reference', 100)->nullable();
            $table->string('subject', 255)->nullable();

            // Status: draft, submitted, approved, rejected, expired, converted
            $table->string('status', 20)->default('draft');

            // Currency support
            $table->string('currency', 3)->default('IDR');
            $table->decimal('exchange_rate', 15, 4)->default(1);

            // Amounts (stored in smallest unit - cents/rupiah)
            $table->bigInteger('subtotal')->default(0);
            $table->string('discount_type', 10)->nullable(); // 'percentage' or 'fixed'
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->bigInteger('discount_amount')->default(0);
            $table->decimal('tax_rate', 5, 2)->default(11.00);
            $table->bigInteger('tax_amount')->default(0);
            $table->bigInteger('total')->default(0);
            $table->bigInteger('base_currency_total')->default(0);

            // Content
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();

            // Workflow timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();

            // Conversion tracking
            $table->foreignId('converted_to_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();

            // Original quotation (for revisions)
            $table->foreignId('original_quotation_id')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('quotation_date');
            $table->index('valid_until');
            $table->index('status');
            $table->index(['contact_id', 'status']);
        });

        // Add self-referencing FK after table creation
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreign('original_quotation_id')
                ->references('id')
                ->on('quotations')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
