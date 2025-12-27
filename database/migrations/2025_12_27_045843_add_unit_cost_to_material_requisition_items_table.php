<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add unit cost fields to material requisition items.
 *
 * This enables cost estimation for material requisitions,
 * useful for budgeting and WIP (work in progress) costing.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_requisition_items', function (Blueprint $table) {
            $table->bigInteger('unit_cost')->default(0)->after('unit');
            $table->bigInteger('estimated_total_cost')->default(0)->after('unit_cost');
        });
    }

    public function down(): void
    {
        Schema::table('material_requisition_items', function (Blueprint $table) {
            $table->dropColumn([
                'unit_cost',
                'estimated_total_cost',
            ]);
        });
    }
};
