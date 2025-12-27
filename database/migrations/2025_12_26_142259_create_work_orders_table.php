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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number', 50)->unique();
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('bom_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('parent_work_order_id')->nullable()->constrained('work_orders')->onDelete('cascade');
            $table->string('type', 20)->default('production'); // production, installation
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity_ordered', 12, 4)->default(1);
            $table->decimal('quantity_completed', 12, 4)->default(0);
            $table->decimal('quantity_scrapped', 12, 4)->default(0);
            $table->string('status', 20)->default('draft'); // draft, confirmed, in_progress, completed, cancelled
            $table->string('priority', 20)->default('normal'); // low, normal, high, urgent
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->bigInteger('estimated_material_cost')->default(0);
            $table->bigInteger('estimated_labor_cost')->default(0);
            $table->bigInteger('estimated_overhead_cost')->default(0);
            $table->bigInteger('estimated_total_cost')->default(0);
            $table->bigInteger('actual_material_cost')->default(0);
            $table->bigInteger('actual_labor_cost')->default(0);
            $table->bigInteger('actual_overhead_cost')->default(0);
            $table->bigInteger('actual_total_cost')->default(0);
            $table->bigInteger('cost_variance')->default(0);
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('started_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('started_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('type');
            $table->index('priority');
            $table->index(['project_id', 'status']);
            $table->index('planned_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
