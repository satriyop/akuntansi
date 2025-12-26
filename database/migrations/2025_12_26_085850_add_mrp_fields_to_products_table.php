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
        Schema::table('products', function (Blueprint $table) {
            // MRP Planning Fields
            $table->integer('reorder_point')->default(0)->after('min_stock');
            $table->integer('safety_stock')->default(0)->after('reorder_point');
            $table->integer('lead_time_days')->default(0)->after('safety_stock');
            $table->decimal('min_order_qty', 12, 4)->default(1)->after('lead_time_days');
            $table->decimal('order_multiple', 12, 4)->default(1)->after('min_order_qty');
            $table->integer('max_stock')->nullable()->after('order_multiple');

            // Default supplier for auto-PO generation
            $table->foreignId('default_supplier_id')->nullable()->after('max_stock')
                ->constrained('contacts')->nullOnDelete();

            // ABC Classification (A = high value/volume, B = medium, C = low)
            $table->string('abc_class', 1)->default('C')->after('default_supplier_id');

            // Procurement type: buy (purchase), make (manufacture), subcontract
            $table->string('procurement_type', 20)->default('buy')->after('abc_class');

            // Indexes
            $table->index('procurement_type');
            $table->index('abc_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['procurement_type']);
            $table->dropIndex(['abc_class']);
            $table->dropForeign(['default_supplier_id']);

            $table->dropColumn([
                'reorder_point',
                'safety_stock',
                'lead_time_days',
                'min_order_qty',
                'order_multiple',
                'max_stock',
                'default_supplier_id',
                'abc_class',
                'procurement_type',
            ]);
        });
    }
};
