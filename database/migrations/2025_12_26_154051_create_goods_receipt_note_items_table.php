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
        Schema::create('goods_receipt_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Quantities
            $table->integer('quantity_ordered')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->integer('quantity_rejected')->default(0);

            // From PO
            $table->bigInteger('unit_price')->default(0);

            // Quality notes
            $table->string('rejection_reason')->nullable();
            $table->text('quality_notes')->nullable();

            // Lot/Batch tracking (optional)
            $table->string('lot_number')->nullable();
            $table->date('expiry_date')->nullable();

            $table->timestamps();

            $table->index('product_id');
            $table->index('goods_receipt_note_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_note_items');
    }
};
