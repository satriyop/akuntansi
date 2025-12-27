<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add line-level discount, tax, and ordering columns to invoice_items.
 *
 * This aligns invoice_items with quotation_items and purchase_order_items,
 * enabling proper data preservation when converting quotations to invoices.
 *
 * Changes:
 * - Rename 'amount' to 'line_total' for consistency
 * - Add discount_percent, discount_amount for line-level discounts
 * - Add tax_rate, tax_amount for mixed tax rate support
 * - Add sort_order for item ordering
 * - Add notes for per-line specifications
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // Add discount columns after unit_price
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');
            $table->bigInteger('discount_amount')->default(0)->after('discount_percent');

            // Add tax columns after discount_amount
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            $table->bigInteger('tax_amount')->default(0)->after('tax_rate');

            // Rename amount to line_total for consistency with other item tables
            $table->renameColumn('amount', 'line_total');

            // Add ordering and notes
            $table->smallInteger('sort_order')->default(0)->after('line_total');
            $table->text('notes')->nullable()->after('sort_order');
        });

        // Add index for sort_order
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percent',
                'discount_amount',
                'tax_rate',
                'tax_amount',
                'sort_order',
                'notes',
            ]);

            $table->renameColumn('line_total', 'amount');
        });
    }
};
