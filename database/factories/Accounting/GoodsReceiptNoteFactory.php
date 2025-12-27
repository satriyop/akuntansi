<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\GoodsReceiptNote;
use App\Models\Accounting\PurchaseOrder;
use App\Models\Accounting\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Accounting\GoodsReceiptNote>
 */
class GoodsReceiptNoteFactory extends Factory
{
    protected $model = GoodsReceiptNote::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = now()->format('Ymd');
        $unique = $this->faker->unique()->numberBetween(1, 9999);

        return [
            'grn_number' => "GRN-{$date}-".str_pad($unique, 4, '0', STR_PAD_LEFT),
            'purchase_order_id' => PurchaseOrder::factory()->approved(),
            'warehouse_id' => Warehouse::factory(),
            'receipt_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => GoodsReceiptNote::STATUS_DRAFT,
            'supplier_do_number' => $this->faker->optional()->bothify('DO-????-####'),
            'supplier_invoice_number' => $this->faker->optional()->bothify('INV-????-####'),
            'vehicle_number' => $this->faker->optional()->bothify('B #### ???'),
            'driver_name' => $this->faker->optional()->name(),
            'notes' => $this->faker->optional()->sentence(),
            'total_items' => 0,
            'total_quantity_ordered' => 0,
            'total_quantity_received' => 0,
            'total_quantity_rejected' => 0,
            'created_by' => User::factory(),
        ];
    }

    /**
     * Indicate the GRN is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => GoodsReceiptNote::STATUS_DRAFT,
        ]);
    }

    /**
     * Indicate the GRN is receiving.
     */
    public function receiving(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => GoodsReceiptNote::STATUS_RECEIVING,
            'received_by' => User::factory(),
        ]);
    }

    /**
     * Indicate the GRN is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => GoodsReceiptNote::STATUS_COMPLETED,
            'received_by' => User::factory(),
            'checked_by' => User::factory(),
            'completed_at' => now(),
        ]);
    }

    /**
     * Indicate the GRN is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => GoodsReceiptNote::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    /**
     * Assign to a specific PO.
     */
    public function forPurchaseOrder(PurchaseOrder $purchaseOrder): static
    {
        return $this->state(fn (array $attributes) => [
            'purchase_order_id' => $purchaseOrder->id,
        ]);
    }

    /**
     * Assign to a specific warehouse.
     */
    public function forWarehouse(Warehouse $warehouse): static
    {
        return $this->state(fn (array $attributes) => [
            'warehouse_id' => $warehouse->id,
        ]);
    }
}
