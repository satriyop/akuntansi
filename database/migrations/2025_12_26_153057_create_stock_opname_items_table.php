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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // System quantities (snapshot at count time)
            $table->integer('system_quantity')->default(0);
            $table->bigInteger('system_cost')->default(0); // Unit cost from ProductStock
            $table->bigInteger('system_value')->default(0); // system_quantity * system_cost

            // Counted quantities
            $table->integer('counted_quantity')->nullable(); // null = not yet counted
            $table->integer('variance_quantity')->default(0); // counted - system
            $table->bigInteger('variance_value')->default(0); // variance_quantity * system_cost

            $table->text('notes')->nullable();
            $table->timestamp('counted_at')->nullable();

            $table->timestamps();

            $table->unique(['stock_opname_id', 'product_id']);
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
