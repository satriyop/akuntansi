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
        // Add project_id to quotations
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('contact_id')->constrained()->onDelete('set null');
            $table->index('project_id');
        });

        // Add project_id to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('contact_id')->constrained()->onDelete('set null');
            $table->index('project_id');
        });

        // Add project_id to bills
        Schema::table('bills', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('contact_id')->constrained()->onDelete('set null');
            $table->index('project_id');
        });

        // Add project_id to purchase_orders
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('contact_id')->constrained()->onDelete('set null');
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('bills', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
