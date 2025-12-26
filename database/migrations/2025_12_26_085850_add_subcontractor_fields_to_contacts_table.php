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
        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('is_subcontractor')->default(false)->after('is_active');
            $table->json('subcontractor_services')->nullable()->after('is_subcontractor');
            $table->bigInteger('hourly_rate')->nullable()->after('subcontractor_services');
            $table->bigInteger('daily_rate')->nullable()->after('hourly_rate');

            $table->index('is_subcontractor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['is_subcontractor']);
            $table->dropColumn([
                'is_subcontractor',
                'subcontractor_services',
                'hourly_rate',
                'daily_rate',
            ]);
        });
    }
};
