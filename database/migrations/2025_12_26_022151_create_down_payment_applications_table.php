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
        Schema::create('down_payment_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('down_payment_id')->constrained()->onDelete('restrict');
            $table->morphs('applicable'); // invoice or bill
            $table->bigInteger('amount'); // Amount applied from DP to this invoice/bill
            $table->date('applied_date');
            $table->text('notes')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['down_payment_id', 'applicable_type', 'applicable_id'], 'dp_application_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('down_payment_applications');
    }
};
