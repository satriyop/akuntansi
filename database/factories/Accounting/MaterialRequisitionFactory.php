<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\MaterialRequisition;
use App\Models\Accounting\Warehouse;
use App\Models\Accounting\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaterialRequisition>
 */
class MaterialRequisitionFactory extends Factory
{
    protected $model = MaterialRequisition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'requisition_number' => 'MR-'.now()->format('Ym').'-'.$this->faker->unique()->numerify('####'),
            'work_order_id' => WorkOrder::factory(),
            'warehouse_id' => null,
            'status' => MaterialRequisition::STATUS_DRAFT,
            'requested_date' => now(),
            'required_date' => now()->addDays(3),
            'total_items' => 0,
            'total_quantity' => 0,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * For specific work order.
     */
    public function forWorkOrder(WorkOrder $workOrder): static
    {
        return $this->state(fn (array $attributes) => [
            'work_order_id' => $workOrder->id,
            'warehouse_id' => $workOrder->warehouse_id,
        ]);
    }

    /**
     * Draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaterialRequisition::STATUS_DRAFT,
        ]);
    }

    /**
     * Approved status.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaterialRequisition::STATUS_APPROVED,
            'approved_at' => now(),
        ]);
    }

    /**
     * Issued status.
     */
    public function issued(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaterialRequisition::STATUS_ISSUED,
            'approved_at' => now()->subDay(),
            'issued_at' => now(),
        ]);
    }

    /**
     * Partial status.
     */
    public function partial(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaterialRequisition::STATUS_PARTIAL,
            'approved_at' => now()->subDay(),
        ]);
    }

    /**
     * Cancelled status.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaterialRequisition::STATUS_CANCELLED,
        ]);
    }

    /**
     * With warehouse.
     */
    public function withWarehouse(?Warehouse $warehouse = null): static
    {
        return $this->state(function (array $attributes) use ($warehouse) {
            $w = $warehouse ?? Warehouse::factory()->create();

            return [
                'warehouse_id' => $w->id,
            ];
        });
    }

    /**
     * With totals.
     */
    public function withTotals(int $items = 5, float $quantity = 50): static
    {
        return $this->state(fn (array $attributes) => [
            'total_items' => $items,
            'total_quantity' => $quantity,
        ]);
    }
}
