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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            $table->string('description', 500);
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('quantity_received', 15, 4)->default(0);
            $table->string('unit', 20)->default('unit');
            $table->bigInteger('unit_price')->default(0);

            // Line discount
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->bigInteger('discount_amount')->default(0);

            // Tax
            $table->decimal('tax_rate', 5, 2)->default(11.00);
            $table->bigInteger('tax_amount')->default(0);

            $table->bigInteger('line_total')->default(0);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('notes')->nullable();

            // Receiving status
            $table->timestamp('last_received_at')->nullable();

            $table->timestamps();

            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
