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
        Schema::create('work_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('bom_item_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('parent_item_id')->nullable()->constrained('work_order_items')->onDelete('cascade');
            $table->string('type', 20)->default('material'); // material, labor, overhead
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description');
            $table->decimal('quantity_required', 12, 4)->default(0);
            $table->decimal('quantity_reserved', 12, 4)->default(0);
            $table->decimal('quantity_consumed', 12, 4)->default(0);
            $table->decimal('quantity_scrapped', 12, 4)->default(0);
            $table->string('unit', 20)->nullable();
            $table->bigInteger('unit_cost')->default(0); // estimated
            $table->bigInteger('actual_unit_cost')->default(0);
            $table->bigInteger('total_estimated_cost')->default(0);
            $table->bigInteger('total_actual_cost')->default(0);
            $table->integer('sort_order')->default(0);
            $table->integer('level')->default(0); // hierarchy level
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index(['work_order_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_items');
    }
};
