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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique(); // SR-YYYYMM-NNNN
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('contact_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->date('return_date');
            $table->string('reason')->nullable(); // damaged, wrong_item, quality_issue, customer_request, etc.
            $table->text('notes')->nullable();
            $table->bigInteger('subtotal')->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->bigInteger('tax_amount')->default(0);
            $table->bigInteger('total_amount')->default(0);
            $table->string('status')->default('draft'); // draft, submitted, approved, completed, cancelled
            $table->foreignId('journal_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('credit_note_id')->nullable()->constrained('invoices')->onDelete('set null'); // Credit note (negative invoice)
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoice_id', 'status']);
            $table->index(['contact_id', 'status']);
            $table->index('return_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
