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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 30);
            $table->unsignedTinyInteger('revision')->default(0);
            $table->foreignId('contact_id')->constrained()->restrictOnDelete(); // Vendor

            $table->date('po_date');
            $table->date('expected_date')->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('subject', 255)->nullable();

            // Status: draft, submitted, approved, rejected, partial, received, cancelled
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
            $table->string('shipping_address', 500)->nullable();

            // Workflow timestamps
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable();

            // Receiving tracking
            $table->timestamp('first_received_at')->nullable();
            $table->timestamp('fully_received_at')->nullable();

            // Conversion tracking
            $table->foreignId('converted_to_bill_id')->nullable()->constrained('bills')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();

            // Original PO (for revisions)
            $table->foreignId('original_po_id')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['po_number', 'revision']);
            $table->index('po_date');
            $table->index('expected_date');
            $table->index('status');
            $table->index(['contact_id', 'status']);
        });

        // Add self-referencing FK after table creation
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreign('original_po_id')
                ->references('id')
                ->on('purchase_orders')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
