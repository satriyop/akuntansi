<?php

namespace App\Services\Accounting;

use App\Models\Accounting\Invoice;
use App\Models\Accounting\InvoiceItem;
use App\Models\Accounting\Quotation;
use App\Models\Accounting\QuotationItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QuotationService
{
    /**
     * Create a new quotation with items.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            // Set defaults
            $data['quotation_number'] = Quotation::generateQuotationNumber();
            $data['status'] = Quotation::STATUS_DRAFT;
            $data['currency'] = $data['currency'] ?? 'IDR';
            $data['exchange_rate'] = $data['exchange_rate'] ?? 1;
            $data['tax_rate'] = $data['tax_rate'] ?? config('accounting.tax.default_rate', 11.00);

            // Set validity if not provided
            if (empty($data['valid_until'])) {
                $quotationDate = $data['quotation_date'] ?? now();
                $validityDays = config('accounting.quotation.default_validity_days', 30);
                $data['valid_until'] = now()->parse($quotationDate)->addDays($validityDays);
            }

            // Set default terms if not provided
            if (empty($data['terms_conditions'])) {
                $data['terms_conditions'] = Quotation::getDefaultTermsConditions();
            }

            // Create quotation with zero totals first
            $data['subtotal'] = 0;
            $data['discount_amount'] = 0;
            $data['tax_amount'] = 0;
            $data['total'] = 0;
            $data['base_currency_total'] = 0;
            $data['created_by'] = auth()->id();

            $quotation = Quotation::create($data);

            // Create items
            $this->createItems($quotation, $items);

            // Calculate totals
            $quotation->refresh();
            $quotation->calculateTotals();
            $quotation->save();

            return $quotation->load('items', 'contact');
        });
    }

    /**
     * Update a quotation.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Quotation $quotation, array $data): Quotation
    {
        if (! $quotation->isEditable()) {
            throw new InvalidArgumentException('Hanya penawaran draft yang dapat diubah.');
        }

        return DB::transaction(function () use ($quotation, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);

            $quotation->update($data);

            if ($items !== null) {
                // Delete existing items and recreate
                $quotation->items()->delete();
                $this->createItems($quotation, $items);
            }

            // Recalculate totals
            $quotation->refresh();
            $quotation->calculateTotals();
            $quotation->save();

            return $quotation->load('items', 'contact');
        });
    }

    /**
     * Submit quotation for approval.
     */
    public function submit(Quotation $quotation, ?int $userId = null): Quotation
    {
        if (! $quotation->canSubmit()) {
            throw new InvalidArgumentException('Penawaran tidak dapat diajukan. Pastikan status draft dan memiliki item.');
        }

        $quotation->update([
            'status' => Quotation::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'submitted_by' => $userId ?? auth()->id(),
        ]);

        return $quotation->fresh(['items', 'contact']);
    }

    /**
     * Approve a quotation.
     */
    public function approve(Quotation $quotation, ?int $userId = null): Quotation
    {
        if (! $quotation->canApprove()) {
            throw new InvalidArgumentException('Penawaran tidak dapat disetujui. Pastikan sudah diajukan dan belum kedaluwarsa.');
        }

        $quotation->update([
            'status' => Quotation::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $userId ?? auth()->id(),
        ]);

        return $quotation->fresh(['items', 'contact']);
    }

    /**
     * Reject a quotation.
     */
    public function reject(Quotation $quotation, string $reason, ?int $userId = null): Quotation
    {
        if (! $quotation->canReject()) {
            throw new InvalidArgumentException('Penawaran tidak dapat ditolak. Pastikan sudah diajukan.');
        }

        if (empty($reason)) {
            throw new InvalidArgumentException('Alasan penolakan harus diisi.');
        }

        $quotation->update([
            'status' => Quotation::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejected_by' => $userId ?? auth()->id(),
            'rejection_reason' => $reason,
        ]);

        return $quotation->fresh(['items', 'contact']);
    }

    /**
     * Create a revision of a quotation.
     */
    public function revise(Quotation $quotation): Quotation
    {
        if (! $quotation->canRevise()) {
            throw new InvalidArgumentException('Penawaran tidak dapat direvisi. Pastikan sudah disetujui, ditolak, atau kedaluwarsa.');
        }

        return DB::transaction(function () use ($quotation) {
            $originalId = $quotation->original_quotation_id ?? $quotation->id;
            $nextRevision = $quotation->getNextRevisionNumber();

            // Create new quotation as revision
            $newQuotation = Quotation::create([
                'quotation_number' => $quotation->quotation_number,
                'revision' => $nextRevision,
                'contact_id' => $quotation->contact_id,
                'quotation_date' => now(),
                'valid_until' => now()->addDays(config('accounting.quotation.default_validity_days', 30)),
                'reference' => $quotation->reference,
                'subject' => $quotation->subject,
                'status' => Quotation::STATUS_DRAFT,
                'currency' => $quotation->currency,
                'exchange_rate' => $quotation->exchange_rate,
                'subtotal' => $quotation->subtotal,
                'discount_type' => $quotation->discount_type,
                'discount_value' => $quotation->discount_value,
                'discount_amount' => $quotation->discount_amount,
                'tax_rate' => $quotation->tax_rate,
                'tax_amount' => $quotation->tax_amount,
                'total' => $quotation->total,
                'base_currency_total' => $quotation->base_currency_total,
                'notes' => $quotation->notes,
                'terms_conditions' => $quotation->terms_conditions,
                'original_quotation_id' => $originalId,
                'created_by' => auth()->id(),
            ]);

            // Copy items
            foreach ($quotation->items as $item) {
                QuotationItem::create([
                    'quotation_id' => $newQuotation->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'discount_percent' => $item->discount_percent,
                    'discount_amount' => $item->discount_amount,
                    'tax_rate' => $item->tax_rate,
                    'tax_amount' => $item->tax_amount,
                    'line_total' => $item->line_total,
                    'sort_order' => $item->sort_order,
                    'notes' => $item->notes,
                ]);
            }

            return $newQuotation->load('items', 'contact');
        });
    }

    /**
     * Convert an approved quotation to an invoice.
     */
    public function convertToInvoice(Quotation $quotation): Invoice
    {
        if (! $quotation->canConvert()) {
            throw new InvalidArgumentException('Penawaran tidak dapat dikonversi. Pastikan sudah disetujui dan belum dikonversi.');
        }

        return DB::transaction(function () use ($quotation) {
            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'contact_id' => $quotation->contact_id,
                'invoice_date' => now(),
                'due_date' => now()->addDays(config('accounting.payment.default_term_days', 30)),
                'description' => $quotation->subject,
                'reference' => $quotation->getFullNumber(),
                'subtotal' => $quotation->subtotal,
                'tax_amount' => $quotation->tax_amount,
                'tax_rate' => $quotation->tax_rate,
                'discount_amount' => $quotation->discount_amount,
                'total_amount' => $quotation->total,
                'currency' => $quotation->currency,
                'exchange_rate' => $quotation->exchange_rate,
                'base_currency_total' => $quotation->base_currency_total,
                'paid_amount' => 0,
                'status' => Invoice::STATUS_DRAFT,
                'created_by' => auth()->id(),
            ]);

            // Copy items
            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'amount' => $item->line_total,
                ]);
            }

            // Update quotation
            $quotation->update([
                'status' => Quotation::STATUS_CONVERTED,
                'converted_to_invoice_id' => $invoice->id,
                'converted_at' => now(),
            ]);

            return $invoice->load('items', 'contact');
        });
    }

    /**
     * Duplicate a quotation as a new draft.
     */
    public function duplicate(Quotation $quotation): Quotation
    {
        return DB::transaction(function () use ($quotation) {
            $newQuotation = Quotation::create([
                'quotation_number' => Quotation::generateQuotationNumber(),
                'revision' => 0,
                'contact_id' => $quotation->contact_id,
                'quotation_date' => now(),
                'valid_until' => now()->addDays(config('accounting.quotation.default_validity_days', 30)),
                'reference' => null,
                'subject' => $quotation->subject,
                'status' => Quotation::STATUS_DRAFT,
                'currency' => $quotation->currency,
                'exchange_rate' => $quotation->exchange_rate,
                'subtotal' => $quotation->subtotal,
                'discount_type' => $quotation->discount_type,
                'discount_value' => $quotation->discount_value,
                'discount_amount' => $quotation->discount_amount,
                'tax_rate' => $quotation->tax_rate,
                'tax_amount' => $quotation->tax_amount,
                'total' => $quotation->total,
                'base_currency_total' => $quotation->base_currency_total,
                'notes' => $quotation->notes,
                'terms_conditions' => $quotation->terms_conditions,
                'created_by' => auth()->id(),
            ]);

            // Copy items
            foreach ($quotation->items as $item) {
                QuotationItem::create([
                    'quotation_id' => $newQuotation->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'discount_percent' => $item->discount_percent,
                    'discount_amount' => $item->discount_amount,
                    'tax_rate' => $item->tax_rate,
                    'tax_amount' => $item->tax_amount,
                    'line_total' => $item->line_total,
                    'sort_order' => $item->sort_order,
                    'notes' => $item->notes,
                ]);
            }

            return $newQuotation->load('items', 'contact');
        });
    }

    /**
     * Mark expired quotations.
     *
     * @return int Number of quotations marked as expired
     */
    public function markExpired(): int
    {
        return Quotation::query()
            ->whereIn('status', [Quotation::STATUS_DRAFT, Quotation::STATUS_SUBMITTED])
            ->where('valid_until', '<', now()->startOfDay())
            ->update(['status' => Quotation::STATUS_EXPIRED]);
    }

    /**
     * Get quotation statistics.
     *
     * @return array<string, mixed>
     */
    public function getStatistics(?string $startDate = null, ?string $endDate = null): array
    {
        $query = Quotation::query();

        if ($startDate) {
            $query->where('quotation_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('quotation_date', '<=', $endDate);
        }

        $total = (clone $query)->count();
        $draft = (clone $query)->where('status', Quotation::STATUS_DRAFT)->count();
        $submitted = (clone $query)->where('status', Quotation::STATUS_SUBMITTED)->count();
        $approved = (clone $query)->where('status', Quotation::STATUS_APPROVED)->count();
        $rejected = (clone $query)->where('status', Quotation::STATUS_REJECTED)->count();
        $expired = (clone $query)->where('status', Quotation::STATUS_EXPIRED)->count();
        $converted = (clone $query)->where('status', Quotation::STATUS_CONVERTED)->count();

        $totalValue = (clone $query)->sum('total');
        $approvedValue = (clone $query)->where('status', Quotation::STATUS_APPROVED)->sum('total');
        $convertedValue = (clone $query)->where('status', Quotation::STATUS_CONVERTED)->sum('total');

        $approvalRate = $total > 0 ? round((($approved + $converted) / $total) * 100, 2) : 0;
        $conversionRate = ($approved + $converted) > 0 ? round(($converted / ($approved + $converted)) * 100, 2) : 0;

        return [
            'total' => $total,
            'by_status' => [
                'draft' => $draft,
                'submitted' => $submitted,
                'approved' => $approved,
                'rejected' => $rejected,
                'expired' => $expired,
                'converted' => $converted,
            ],
            'total_value' => $totalValue,
            'approved_value' => $approvedValue,
            'converted_value' => $convertedValue,
            'approval_rate' => $approvalRate,
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * Create quotation items.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    private function createItems(Quotation $quotation, array $items): void
    {
        foreach ($items as $index => $itemData) {
            $quantity = $itemData['quantity'] ?? 1;
            $unitPrice = $itemData['unit_price'] ?? 0;
            $discountPercent = $itemData['discount_percent'] ?? 0;
            $taxRate = $itemData['tax_rate'] ?? $quotation->tax_rate;

            $grossAmount = (int) round($quantity * $unitPrice);
            $discountAmount = $discountPercent > 0
                ? (int) round($grossAmount * ($discountPercent / 100))
                : 0;
            $netAmount = $grossAmount - $discountAmount;
            $taxAmount = (int) round($netAmount * ($taxRate / 100));

            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $itemData['product_id'] ?? null,
                'description' => $itemData['description'],
                'quantity' => $quantity,
                'unit' => $itemData['unit'] ?? 'unit',
                'unit_price' => $unitPrice,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'line_total' => $netAmount,
                'sort_order' => $itemData['sort_order'] ?? $index,
                'notes' => $itemData['notes'] ?? null,
            ]);
        }
    }
}
