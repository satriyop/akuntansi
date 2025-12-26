<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Accounting\Quotation
 */
class QuotationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quotation_number' => $this->quotation_number,
            'revision' => $this->revision,
            'full_number' => $this->getFullNumber(),

            'contact_id' => $this->contact_id,
            'contact' => new ContactResource($this->whenLoaded('contact')),

            'quotation_date' => $this->quotation_date->toDateString(),
            'valid_until' => $this->valid_until->toDateString(),
            'days_until_expiry' => $this->getDaysUntilExpiry(),
            'is_expired' => $this->isExpired(),

            'reference' => $this->reference,
            'subject' => $this->subject,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),

            'currency' => $this->currency,
            'exchange_rate' => (float) $this->exchange_rate,

            'subtotal' => $this->subtotal,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'discount_amount' => $this->discount_amount,
            'tax_rate' => (float) $this->tax_rate,
            'tax_amount' => $this->tax_amount,
            'total' => $this->total,
            'base_currency_total' => $this->base_currency_total,

            'notes' => $this->notes,
            'terms_conditions' => $this->terms_conditions,

            // Workflow info
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'submitted_by' => $this->submitted_by,
            'approved_at' => $this->approved_at?->toIso8601String(),
            'approved_by' => $this->approved_by,
            'rejected_at' => $this->rejected_at?->toIso8601String(),
            'rejected_by' => $this->rejected_by,
            'rejection_reason' => $this->rejection_reason,

            'converted_to_invoice_id' => $this->converted_to_invoice_id,
            'converted_at' => $this->converted_at?->toIso8601String(),

            'original_quotation_id' => $this->original_quotation_id,

            // Permissions
            'can_edit' => $this->isEditable(),
            'can_submit' => $this->canSubmit(),
            'can_approve' => $this->canApprove(),
            'can_reject' => $this->canReject(),
            'can_convert' => $this->canConvert(),
            'can_revise' => $this->canRevise(),

            // Relations
            'items' => QuotationItemResource::collection($this->whenLoaded('items')),
            'revisions' => QuotationResource::collection($this->whenLoaded('revisions')),
            'converted_invoice' => new InvoiceResource($this->whenLoaded('convertedInvoice')),

            'created_by' => $this->created_by,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
