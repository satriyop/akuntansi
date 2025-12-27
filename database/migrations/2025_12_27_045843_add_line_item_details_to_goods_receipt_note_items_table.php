<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add line-level discount, tax, and ordering columns to goods_receipt_note_items.
 *
 * This aligns GRN items with purchase_order_items structure,
 * enabling proper data preservation when receiving from POs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('goods_receipt_note_items', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0)->after('unit_price');
            $table->bigInteger('discount_amount')->default(0)->after('discount_percent');
            $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            $table->bigInteger('tax_amount')->default(0)->after('tax_rate');
            $table->bigInteger('line_total')->default(0)->after('tax_amount');
            $table->smallInteger('sort_order')->default(0)->after('line_total');
            $table->string('unit', 50)->nullable()->after('product_id');
            $table->text('description')->nullable()->after('unit');
        });

        Schema::table('goods_receipt_note_items', function (Blueprint $table) {
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('goods_receipt_note_items', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

        Schema::table('goods_receipt_note_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit',
                'description',
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
