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
        Schema::create('goods_receipt_notes', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number', 30)->unique();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();

            $table->date('receipt_date');

            // Status: draft, receiving, completed, cancelled
            $table->string('status', 20)->default('draft');

            // Supplier references
            $table->string('supplier_do_number')->nullable(); // Surat jalan supplier
            $table->string('supplier_invoice_number')->nullable();

            // Shipping info
            $table->string('vehicle_number')->nullable();
            $table->string('driver_name')->nullable();

            // Staff tracking
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();

            // Summary (calculated)
            $table->integer('total_items')->default(0);
            $table->integer('total_quantity_ordered')->default(0);
            $table->integer('total_quantity_received')->default(0);
            $table->integer('total_quantity_rejected')->default(0);

            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('receipt_date');
            $table->index('purchase_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_notes');
    }
};
