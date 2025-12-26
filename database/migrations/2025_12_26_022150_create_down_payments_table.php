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
        Schema::create('down_payments', function (Blueprint $table) {
            $table->id();
            $table->string('dp_number')->unique();
            $table->enum('type', ['receivable', 'payable']); // receivable = from customer, payable = to vendor
            $table->foreignId('contact_id')->constrained()->onDelete('restrict');
            $table->date('dp_date');
            $table->bigInteger('amount'); // Total down payment amount
            $table->bigInteger('applied_amount')->default(0); // Amount already applied to invoices/bills
            $table->bigInteger('remaining_amount')->storedAs('amount - applied_amount'); // Virtual column
            $table->string('payment_method')->default('bank_transfer'); // bank_transfer, cash, check, etc.
            $table->foreignId('cash_account_id')->constrained('accounts')->onDelete('restrict');
            $table->string('reference')->nullable(); // External reference (e.g., quotation, PO number)
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, fully_applied, refunded, cancelled
            $table->foreignId('journal_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('refund_payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->timestamp('refunded_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['contact_id', 'type']);
            $table->index(['status', 'type']);
            $table->index('dp_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('down_payments');
    }
};
