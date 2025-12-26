<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesReturnItem extends Model
{
    use HasFactory;

    public const CONDITION_GOOD = 'good';

    public const CONDITION_DAMAGED = 'damaged';

    public const CONDITION_DEFECTIVE = 'defective';

    protected $fillable = [
        'sales_return_id',
        'invoice_item_id',
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
     * @return BelongsTo<SalesReturn, $this>
     */
    public function salesReturn(): BelongsTo
    {
        return $this->belongsTo(SalesReturn::class);
    }

    /**
     * @return BelongsTo<InvoiceItem, $this>
     */
    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(InvoiceItem::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Fill item data from an invoice item.
     */
    public function fillFromInvoiceItem(InvoiceItem $invoiceItem): void
    {
        $this->invoice_item_id = $invoiceItem->id;
        $this->product_id = $invoiceItem->product_id;
        $this->description = $invoiceItem->description;
        $this->quantity = $invoiceItem->quantity;
        $this->unit = $invoiceItem->unit;
        $this->unit_price = $invoiceItem->unit_price;
        $this->amount = $invoiceItem->amount;
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
