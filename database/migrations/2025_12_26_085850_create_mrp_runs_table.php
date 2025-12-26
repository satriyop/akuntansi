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
        Schema::create('mrp_runs', function (Blueprint $table) {
            $table->id();
            $table->string('run_number')->unique();
            $table->string('name');
            $table->date('planning_horizon_start');
            $table->date('planning_horizon_end');
            $table->string('status', 20)->default('draft'); // draft, processing, completed, applied

            // Run parameters
            $table->json('parameters')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();

            // Results summary
            $table->integer('total_products_analyzed')->default(0);
            $table->integer('total_demands')->default(0);
            $table->integer('total_shortages')->default(0);
            $table->integer('total_purchase_suggestions')->default(0);
            $table->integer('total_work_order_suggestions')->default(0);
            $table->integer('total_subcontract_suggestions')->default(0);

            // Workflow tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('planning_horizon_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mrp_runs');
    }
};
