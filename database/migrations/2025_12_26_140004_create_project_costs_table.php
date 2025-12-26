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
        Schema::create('project_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('cost_type'); // material, labor, subcontractor, equipment, overhead, other
            $table->string('description');
            $table->date('cost_date');
            $table->decimal('quantity', 15, 4)->default(1);
            $table->string('unit')->nullable();
            $table->bigInteger('unit_cost')->default(0);
            $table->bigInteger('total_cost')->default(0);
            $table->string('reference_type')->nullable(); // Bill, PurchaseOrder, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('bill_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('vendor_name')->nullable();
            $table->boolean('is_billable')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['project_id', 'cost_type']);
            $table->index('cost_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_costs');
    }
};
