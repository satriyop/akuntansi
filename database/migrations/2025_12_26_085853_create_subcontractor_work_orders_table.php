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
        Schema::create('subcontractor_work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('sc_wo_number')->unique();
            $table->foreignId('subcontractor_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->text('scope_of_work')->nullable();
            $table->string('status', 20)->default('draft'); // draft, assigned, in_progress, completed, cancelled

            // Financials
            $table->bigInteger('agreed_amount')->default(0);
            $table->bigInteger('actual_amount')->default(0);
            $table->decimal('retention_percent', 5, 2)->default(5);
            $table->bigInteger('retention_amount')->default(0);
            $table->bigInteger('amount_invoiced')->default(0);
            $table->bigInteger('amount_paid')->default(0);
            $table->bigInteger('amount_due')->default(0);

            // Schedule
            $table->date('scheduled_start_date')->nullable();
            $table->date('scheduled_end_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->integer('completion_percentage')->default(0);

            // Location (for installation/field work)
            $table->string('work_location')->nullable();
            $table->text('location_address')->nullable();

            $table->text('notes')->nullable();

            // Workflow tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('started_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['subcontractor_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index('scheduled_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subcontractor_work_orders');
    }
};
