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
        Schema::create('material_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_requisition_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_order_item_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_requested', 12, 4)->default(0);
            $table->decimal('quantity_approved', 12, 4)->default(0);
            $table->decimal('quantity_issued', 12, 4)->default(0);
            $table->decimal('quantity_pending', 12, 4)->default(0);
            $table->string('unit', 20)->nullable();
            $table->string('warehouse_location', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['material_requisition_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_requisition_items');
    }
};
