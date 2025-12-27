<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add revenue account to quotation items for proper invoice conversion.
 *
 * This enables carrying over the designated revenue account when
 * converting quotation items to invoice items.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->foreignId('revenue_account_id')->nullable()->after('notes')
                ->constrained('accounts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropForeign(['revenue_account_id']);
            $table->dropColumn('revenue_account_id');
        });
    }
};
