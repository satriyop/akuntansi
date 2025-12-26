<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\BillItem;
use App\Models\Accounting\Product;
use App\Models\Accounting\PurchaseReturn;
use App\Models\Accounting\PurchaseReturnItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseReturnItem>
 */
class PurchaseReturnItemFactory extends Factory
{
    protected $model = PurchaseReturnItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 10);
        $unitPrice = $this->faker->numberBetween(10000, 500000);
        $amount = (int) round($quantity * $unitPrice);

        return [
            'purchase_return_id' => PurchaseReturn::factory(),
            'bill_item_id' => null,
            'product_id' => null,
            'description' => $this->faker->words(3, true),
            'quantity' => $quantity,
            'unit' => $this->faker->randomElement(['pcs', 'unit', 'set', 'box', 'kg', 'm']),
            'unit_price' => $unitPrice,
            'amount' => $amount,
            'condition' => $this->faker->randomElement([
                PurchaseReturnItem::CONDITION_GOOD,
                PurchaseReturnItem::CONDITION_DAMAGED,
                PurchaseReturnItem::CONDITION_DEFECTIVE,
            ]),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * For specific purchase return.
     */
    public function forPurchaseReturn(PurchaseReturn $purchaseReturn): static
    {
        return $this->state(fn (array $attributes) => [
            'purchase_return_id' => $purchaseReturn->id,
        ]);
    }

    /**
     * From bill item.
     */
    public function fromBillItem(?BillItem $billItem = null): static
    {
        return $this->state(function (array $attributes) use ($billItem) {
            $item = $billItem ?? BillItem::factory()->create();

            return [
                'bill_item_id' => $item->id,
                'product_id' => $item->product_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit' => $item->unit,
                'unit_price' => $item->unit_price,
                'amount' => $item->amount,
            ];
        });
    }

    /**
     * With product.
     */
    public function withProduct(?Product $product = null): static
    {
        return $this->state(function (array $attributes) use ($product) {
            $p = $product ?? Product::factory()->create();

            return [
                'product_id' => $p->id,
                'description' => $p->name,
                'unit' => $p->unit,
            ];
        });
    }

    /**
     * With specific quantity.
     */
    public function withQuantity(float $quantity): static
    {
        return $this->state(function (array $attributes) use ($quantity) {
            $unitPrice = $attributes['unit_price'] ?? 100000;
            $amount = (int) round($quantity * $unitPrice);

            return [
                'quantity' => $quantity,
                'amount' => $amount,
            ];
        });
    }

    /**
     * With specific amount.
     */
    public function withAmount(int $unitPrice, float $quantity = 1): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'amount' => (int) round($quantity * $unitPrice),
        ]);
    }

    /**
     * Good condition.
     */
    public function goodCondition(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => PurchaseReturnItem::CONDITION_GOOD,
        ]);
    }

    /**
     * Damaged condition.
     */
    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => PurchaseReturnItem::CONDITION_DAMAGED,
        ]);
    }

    /**
     * Defective condition.
     */
    public function defective(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => PurchaseReturnItem::CONDITION_DEFECTIVE,
        ]);
    }
}
