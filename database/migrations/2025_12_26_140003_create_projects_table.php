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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_number')->unique(); // PRJ-YYYYMM-NNNN
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('contact_id')->constrained()->onDelete('restrict'); // Customer
            $table->foreignId('quotation_id')->nullable()->constrained()->onDelete('set null');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->string('status')->default('draft'); // draft, planning, in_progress, on_hold, completed, cancelled
            $table->bigInteger('budget_amount')->default(0);
            $table->bigInteger('contract_amount')->default(0); // Nilai kontrak
            $table->bigInteger('total_cost')->default(0); // Actual costs
            $table->bigInteger('total_revenue')->default(0); // From invoices
            $table->bigInteger('gross_profit')->default(0); // revenue - cost
            $table->decimal('profit_margin', 8, 2)->default(0); // (profit / revenue) * 100
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->text('location')->nullable(); // Site location
            $table->text('notes')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['contact_id', 'status']);
            $table->index('status');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
