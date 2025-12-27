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
        Schema::create('subcontractor_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->foreignId('subcontractor_work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcontractor_id')->constrained('contacts')->cascadeOnDelete();

            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            // Amounts
            $table->bigInteger('gross_amount');
            $table->bigInteger('retention_held')->default(0);
            $table->bigInteger('other_deductions')->default(0);
            $table->bigInteger('net_amount');

            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending'); // pending, approved, rejected, paid

            // Link to AP bill when converted
            $table->foreignId('bill_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('converted_to_bill_at')->nullable();

            // Workflow
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['subcontractor_id', 'status']);
            $table->index('invoice_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_invoices');
    }
};
