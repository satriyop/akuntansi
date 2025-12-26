<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReturnItem extends Model
{
    use HasFactory;

    public const CONDITION_GOOD = 'good';

    public const CONDITION_DAMAGED = 'damaged';

    public const CONDITION_DEFECTIVE = 'defective';

    protected $fillable = [
        'purchase_return_id',
        'bill_item_id',
        'product_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'amount',
        'condition',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'unit_price' => 'integer',
            'amount' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<PurchaseReturn, $this>
     */
    public function purchaseReturn(): BelongsTo
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    /**
     * @return BelongsTo<BillItem, $this>
     */
    public function billItem(): BelongsTo
    {
        return $this->belongsTo(BillItem::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Fill item data from a bill item.
     */
    public function fillFromBillItem(BillItem $billItem): void
    {
        $this->bill_item_id = $billItem->id;
        $this->product_id = $billItem->product_id;
        $this->description = $billItem->description;
        $this->quantity = $billItem->quantity;
        $this->unit = $billItem->unit;
        $this->unit_price = $billItem->unit_price;
        $this->amount = $billItem->amount;
    }

    /**
     * Calculate amount from quantity and unit price.
     */
    public function calculateAmount(): void
    {
        $this->amount = (int) round((float) $this->quantity * $this->unit_price);
    }

    /**
     * Get available conditions.
     *
     * @return array<string, string>
     */
    public static function getConditions(): array
    {
        return [
            self::CONDITION_GOOD => 'Baik',
            self::CONDITION_DAMAGED => 'Rusak',
            self::CONDITION_DEFECTIVE => 'Cacat',
        ];
    }
}
