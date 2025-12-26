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
        Schema::create('mrp_demands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mrp_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Demand source (polymorphic)
            $table->string('demand_source_type'); // WorkOrder, Project
            $table->unsignedBigInteger('demand_source_id');
            $table->string('demand_source_number')->nullable(); // WO number, Project number

            // Timing
            $table->date('required_date');
            $table->integer('week_bucket')->nullable(); // Week number for grouping

            // Quantities
            $table->decimal('quantity_required', 12, 4);
            $table->decimal('quantity_on_hand', 12, 4)->default(0);
            $table->decimal('quantity_on_order', 12, 4)->default(0); // From open POs
            $table->decimal('quantity_reserved', 12, 4)->default(0);
            $table->decimal('quantity_available', 12, 4)->default(0);
            $table->decimal('quantity_short', 12, 4)->default(0);

            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('bom_level')->default(0); // 0 = direct demand, 1+ = exploded from parent

            $table->timestamps();

            $table->index(['mrp_run_id', 'product_id']);
            $table->index('required_date');
            $table->index(['demand_source_type', 'demand_source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrp_demands');
    }
};
