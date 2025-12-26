<?php

namespace Database\Factories\Accounting;

use App\Models\Accounting\InvoiceItem;
use App\Models\Accounting\Product;
use App\Models\Accounting\SalesReturn;
use App\Models\Accounting\SalesReturnItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesReturnItem>
 */
class SalesReturnItemFactory extends Factory
{
    protected $model = SalesReturnItem::class;

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
            'sales_return_id' => SalesReturn::factory(),
            'invoice_item_id' => null,
            'product_id' => null,
            'description' => $this->faker->words(3, true),
            'quantity' => $quantity,
            'unit' => $this->faker->randomElement(['pcs', 'unit', 'set', 'box', 'kg', 'm']),
            'unit_price' => $unitPrice,
            'amount' => $amount,
            'condition' => $this->faker->randomElement([
                SalesReturnItem::CONDITION_GOOD,
                SalesReturnItem::CONDITION_DAMAGED,
                SalesReturnItem::CONDITION_DEFECTIVE,
            ]),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * For specific sales return.
     */
    public function forSalesReturn(SalesReturn $salesReturn): static
    {
        return $this->state(fn (array $attributes) => [
            'sales_return_id' => $salesReturn->id,
        ]);
    }

    /**
     * From invoice item.
     */
    public function fromInvoiceItem(?InvoiceItem $invoiceItem = null): static
    {
        return $this->state(function (array $attributes) use ($invoiceItem) {
            $item = $invoiceItem ?? InvoiceItem::factory()->create();

            return [
                'invoice_item_id' => $item->id,
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
            'condition' => SalesReturnItem::CONDITION_GOOD,
        ]);
    }

    /**
     * Damaged condition.
     */
    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => SalesReturnItem::CONDITION_DAMAGED,
        ]);
    }

    /**
     * Defective condition.
     */
    public function defective(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => SalesReturnItem::CONDITION_DEFECTIVE,
        ]);
    }
}
