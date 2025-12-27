<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add progress_percentage column to work_orders for dashboard displays.
 *
 * This provides a cached progress value that can be displayed without
 * recalculating quantity_completed / quantity_ordered on every query.
 *
 * The value should be updated when quantity_completed changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress_percentage')
                ->default(0)
                ->after('priority')
                ->comment('Cached progress: 0-100');
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('progress_percentage');
        });
    }
};
