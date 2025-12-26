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
        Schema::create('mrp_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mrp_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Suggestion type: purchase, work_order, subcontract
            $table->string('suggestion_type', 20);
            // Action: create, expedite, defer, cancel
            $table->string('action', 20)->default('create');

            // Timing (with lead time offset)
            $table->date('suggested_order_date'); // When to place order
            $table->date('suggested_due_date'); // When needed

            // Quantities
            $table->decimal('quantity_required', 12, 4);
            $table->decimal('suggested_quantity', 12, 4); // After MOQ/order multiple rounding
            $table->decimal('adjusted_quantity', 12, 4)->nullable(); // User adjustment

            // Supplier (for purchase suggestions)
            $table->foreignId('suggested_supplier_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('suggested_warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();

            // Cost estimate
            $table->bigInteger('estimated_unit_cost')->nullable();
            $table->bigInteger('estimated_total_cost')->nullable();

            // Priority based on urgency
            $table->string('priority', 10)->default('normal'); // low, normal, high, urgent

            // Status tracking
            $table->string('status', 20)->default('pending'); // pending, accepted, rejected, converted
            $table->text('reason')->nullable(); // Why this suggestion was made
            $table->text('notes')->nullable();

            // Reference to created document (after conversion)
            $table->string('converted_to_type')->nullable(); // PurchaseOrder, WorkOrder, SubcontractorWorkOrder
            $table->unsignedBigInteger('converted_to_id')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('converted_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['mrp_run_id', 'suggestion_type']);
            $table->index('status');
            $table->index('priority');
            $table->index('suggested_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrp_suggestions');
    }
};
