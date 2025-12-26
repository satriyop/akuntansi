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
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('material'); // material, labor, overhead
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null'); // For materials
            $table->string('description');
            $table->decimal('quantity', 15, 4);
            $table->string('unit')->nullable();
            $table->bigInteger('unit_cost')->default(0);
            $table->bigInteger('total_cost')->default(0); // quantity * unit_cost
            $table->decimal('waste_percentage', 5, 2)->default(0); // Allowance for waste
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['bom_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bom_items');
    }
};
