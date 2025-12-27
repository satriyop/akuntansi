<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add line-level discount, tax, and ordering columns to sales_return_items.
 *
 * This aligns sales_return_items with invoice_items structure,
 * enabling proper data preservation when creating returns from invoices.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');
            $table->bigInteger('discount_amount')->default(0)->after('discount_percent');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            $table->bigInteger('tax_amount')->default(0)->after('tax_rate');
            $table->renameColumn('amount', 'line_total');
            $table->smallInteger('sort_order')->default(0)->after('line_total');
        });

        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percent',
                'discount_amount',
                'tax_rate',
                'tax_amount',
                'sort_order',
            ]);
            $table->renameColumn('line_total', 'amount');
        });
    }
};
