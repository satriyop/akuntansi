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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_number')->unique(); // DO-YYYYMM-NNNN
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('contact_id')->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->nullable()->constrained()->onDelete('set null');
            $table->date('do_date');
            $table->date('shipping_date')->nullable();
            $table->date('received_date')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_method')->nullable(); // courier, pickup, own_delivery
            $table->string('tracking_number')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft, confirmed, shipped, delivered, cancelled
            $table->string('received_by')->nullable(); // Name of person who received
            $table->text('delivery_notes')->nullable(); // Notes on delivery
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('shipped_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('shipped_at')->nullable();
            $table->foreignId('delivered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['invoice_id', 'status']);
            $table->index(['contact_id', 'status']);
            $table->index('do_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
