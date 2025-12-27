<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add pricing fields to delivery order items for financial tracking.
 *
 * This enables carrying over pricing information from invoice items
 * and calculating delivery order value.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->bigInteger('unit_price')->default(0)->after('unit');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');
            $table->bigInteger('discount_amount')->default(0)->after('discount_percent');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            $table->bigInteger('tax_amount')->default(0)->after('tax_rate');
            $table->bigInteger('line_total')->default(0)->after('tax_amount');
            $table->smallInteger('sort_order')->default(0)->after('line_total');
        });

        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('delivery_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit_price',
                'discount_percent',
                'discount_amount',
                'tax_rate',
                'tax_amount',
                'line_total',
                'sort_order',
            ]);
        });
    }
};
