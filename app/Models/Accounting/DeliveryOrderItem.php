<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_order_id',
        'invoice_item_id',
        'product_id',
        'description',
        'quantity',
        'quantity_delivered',
        'unit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'quantity_delivered' => 'decimal:4',
        ];
    }

    /**
     * @return BelongsTo<DeliveryOrder, $this>
     */
    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(DeliveryOrder::class);
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
     * Get remaining quantity to deliver.
     */
    public function getRemainingQuantity(): float
    {
        return (float) $this->quantity - (float) $this->quantity_delivered;
    }

    /**
     * Check if item is fully delivered.
     */
    public function isFullyDelivered(): bool
    {
        return (float) $this->quantity_delivered >= (float) $this->quantity;
    }

    /**
     * Fill from invoice item.
     */
    public function fillFromInvoiceItem(InvoiceItem $invoiceItem, ?float $quantity = null): void
    {
        $this->invoice_item_id = $invoiceItem->id;
        $this->product_id = $invoiceItem->product_id;
        $this->description = $invoiceItem->description;
        $this->quantity = $quantity ?? $invoiceItem->quantity;
        $this->unit = $invoiceItem->unit;
    }

    /**
     * Fill from product.
     */
    public function fillFromProduct(Product $product, float $quantity): void
    {
        $this->product_id = $product->id;
        $this->description = $product->name;
        $this->quantity = $quantity;
        $this->unit = $product->unit;
    }
}
