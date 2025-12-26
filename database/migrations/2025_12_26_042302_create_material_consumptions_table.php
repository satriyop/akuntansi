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
        Schema::create('material_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_order_item_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_consumed', 12, 4)->default(0);
            $table->decimal('quantity_scrapped', 12, 4)->default(0);
            $table->string('scrap_reason', 255)->nullable();
            $table->string('unit', 20)->nullable();
            $table->bigInteger('unit_cost')->default(0); // actual cost at consumption
            $table->bigInteger('total_cost')->default(0);
            $table->date('consumed_date');
            $table->string('batch_number', 50)->nullable(); // for future lot tracking
            $table->foreignId('consumed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['work_order_id', 'product_id']);
            $table->index('consumed_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_consumptions');
    }
};
