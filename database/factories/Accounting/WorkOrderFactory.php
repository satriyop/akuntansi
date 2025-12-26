<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\Bom;
use App\Models\Accounting\Product;
use App\Models\Accounting\Project;
use App\Models\Accounting\Warehouse;
use App\Models\Accounting\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    protected $model = WorkOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plannedStart = $this->faker->dateTimeBetween('-1 week', '+1 week');
        $plannedEnd = $this->faker->dateTimeBetween($plannedStart, '+1 month');

        return [
            'wo_number' => 'WO-'.now()->format('Ym').'-'.$this->faker->unique()->numerify('####'),
            'project_id' => null,
            'bom_id' => null,
            'product_id' => Product::factory(),
            'parent_work_order_id' => null,
            'type' => WorkOrder::TYPE_PRODUCTION,
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional()->sentence(),
            'quantity_ordered' => $this->faker->numberBetween(1, 10),
            'quantity_completed' => 0,
            'quantity_scrapped' => 0,
            'status' => WorkOrder::STATUS_DRAFT,
            'priority' => WorkOrder::PRIORITY_NORMAL,
            'planned_start_date' => $plannedStart,
            'planned_end_date' => $plannedEnd,
            'actual_start_date' => null,
            'actual_end_date' => null,
            'estimated_material_cost' => 0,
            'estimated_labor_cost' => 0,
            'estimated_overhead_cost' => 0,
            'estimated_total_cost' => 0,
            'actual_material_cost' => 0,
            'actual_labor_cost' => 0,
            'actual_overhead_cost' => 0,
            'actual_total_cost' => 0,
            'cost_variance' => 0,
            'warehouse_id' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Production type.
     */
    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WorkOrder::TYPE_PRODUCTION,
        ]);
    }

    /**
     * Installation type.
     */
    public function installation(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WorkOrder::TYPE_INSTALLATION,
        ]);
    }

    /**
     * Draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkOrder::STATUS_DRAFT,
        ]);
    }

    /**
     * Confirmed status.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkOrder::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * In progress status.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkOrder::STATUS_IN_PROGRESS,
            'confirmed_at' => now()->subDay(),
            'started_at' => now(),
            'actual_start_date' => now(),
        ]);
    }

    /**
     * Completed status.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $quantity = $attributes['quantity_ordered'] ?? 1;

            return [
                'status' => WorkOrder::STATUS_COMPLETED,
                'confirmed_at' => now()->subWeek(),
                'started_at' => now()->subDays(5),
                'completed_at' => now(),
                'actual_start_date' => now()->subDays(5),
                'actual_end_date' => now(),
                'quantity_completed' => $quantity,
            ];
        });
    }

    /**
     * Cancelled status.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WorkOrder::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Dibatalkan oleh pengguna',
        ]);
    }

    /**
     * For specific project.
     */
    public function forProject(Project $project): static
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
            'wo_number' => $project->project_number.'-WO-'.$this->faker->unique()->numerify('###'),
        ]);
    }

    /**
     * With BOM.
     */
    public function withBom(?Bom $bom = null): static
    {
        return $this->state(function (array $attributes) use ($bom) {
            $b = $bom ?? Bom::factory()->create();

            return [
                'bom_id' => $b->id,
                'product_id' => $b->product_id,
                'name' => $b->name,
            ];
        });
    }

    /**
     * For specific product.
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
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
     * With estimated costs.
     */
    public function withEstimatedCosts(
        int $materialCost = 1000000,
        int $laborCost = 500000,
        int $overheadCost = 200000
    ): static {
        return $this->state(fn (array $attributes) => [
            'estimated_material_cost' => $materialCost,
            'estimated_labor_cost' => $laborCost,
            'estimated_overhead_cost' => $overheadCost,
            'estimated_total_cost' => $materialCost + $laborCost + $overheadCost,
        ]);
    }

    /**
     * With actual costs.
     */
    public function withActualCosts(
        int $materialCost = 1100000,
        int $laborCost = 550000,
        int $overheadCost = 220000
    ): static {
        return $this->state(function (array $attributes) use ($materialCost, $laborCost, $overheadCost) {
            $actual = $materialCost + $laborCost + $overheadCost;
            $estimated = $attributes['estimated_total_cost'] ?? $actual;

            return [
                'actual_material_cost' => $materialCost,
                'actual_labor_cost' => $laborCost,
                'actual_overhead_cost' => $overheadCost,
                'actual_total_cost' => $actual,
                'cost_variance' => $estimated - $actual,
            ];
        });
    }

    /**
     * High priority.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => WorkOrder::PRIORITY_HIGH,
        ]);
    }

    /**
     * Urgent priority.
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => WorkOrder::PRIORITY_URGENT,
        ]);
    }

    /**
     * As sub-work order.
     */
    public function subWorkOrder(WorkOrder $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_work_order_id' => $parent->id,
            'project_id' => $parent->project_id,
            'warehouse_id' => $parent->warehouse_id,
        ]);
    }
}
