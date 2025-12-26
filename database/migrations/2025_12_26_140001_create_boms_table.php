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
        Schema::create('boms', function (Blueprint $table) {
            $table->id();
            $table->string('bom_number')->unique(); // BOM-YYYYMM-NNNN
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Finished product
            $table->decimal('output_quantity', 15, 4)->default(1); // How many units this BOM produces
            $table->string('output_unit')->nullable();
            $table->bigInteger('total_material_cost')->default(0);
            $table->bigInteger('total_labor_cost')->default(0);
            $table->bigInteger('total_overhead_cost')->default(0);
            $table->bigInteger('total_cost')->default(0);
            $table->bigInteger('unit_cost')->default(0); // total_cost / output_quantity
            $table->string('status')->default('draft'); // draft, active, inactive
            $table->string('version')->default('1.0');
            $table->foreignId('parent_bom_id')->nullable()->constrained('boms')->onDelete('set null'); // For versioning
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boms');
    }
};
