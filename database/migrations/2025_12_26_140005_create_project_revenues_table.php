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
        Schema::create('project_revenues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('revenue_type'); // invoice, down_payment, milestone, other
            $table->string('description');
            $table->date('revenue_date');
            $table->bigInteger('amount')->default(0);
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('down_payment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('milestone_name')->nullable();
            $table->decimal('milestone_percentage', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['project_id', 'revenue_type']);
            $table->index('revenue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_revenues');
    }
};
