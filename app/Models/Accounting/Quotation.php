<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CONVERTED = 'converted';

    protected $fillable = [
        'quotation_number',
        'revision',
        'contact_id',
        'project_id',
        'quotation_date',
        'valid_until',
        'reference',
        'subject',
        'status',
        'currency',
        'exchange_rate',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'total',
        'base_currency_total',
        'notes',
        'terms_conditions',
        'submitted_at',
        'submitted_by',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'converted_to_invoice_id',
        'converted_at',
        'original_quotation_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quotation_date' => 'date',
            'valid_until' => 'date',
            'revision' => 'integer',
            'exchange_rate' => 'decimal:4',
            'subtotal' => 'integer',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'integer',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'integer',
            'total' => 'integer',
            'base_currency_total' => 'integer',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * @return HasMany<QuotationItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * @return BelongsTo<Invoice, $this>
     */
    public function convertedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'converted_to_invoice_id');
    }

    /**
     * @return BelongsTo<Quotation, $this>
     */
    public function originalQuotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'original_quotation_id');
    }

    /**
     * @return HasMany<Quotation, $this>
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(Quotation::class, 'original_quotation_id');
    }

    /**
     * @return MorphMany<Attachment, $this>
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Scope for draft quotations.
     *
     * @param  Builder<Quotation>  $query
     * @return Builder<Quotation>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope for submitted quotations.
     *
     * @param  Builder<Quotation>  $query
     * @return Builder<Quotation>
     */
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    /**
     * Scope for approved quotations.
     *
     * @param  Builder<Quotation>  $query
     * @return Builder<Quotation>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for pending quotations (submitted but not decided).
     *
     * @param  Builder<Quotation>  $query
     * @return Builder<Quotation>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    /**
     * Scope for active quotations (not expired/converted/rejected).
     *
     * @param  Builder<Quotation>  $query
     * @return Builder<Quotation>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            self::STATUS_EXPIRED,
            self::STATUS_CONVERTED,
            self::STATUS_REJECTED,
        ]);
    }

    /**
     * Scope for expired quotations.
     *
     * @param  Builder<Quotation>  $query
     * @return Builder<Quotation>
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_EXPIRED);
    }

    /**
     * Check if quotation is editable.
     */
    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if quotation can be submitted.
     */
    public function canSubmit(): bool
    {
        return $this->status === self::STATUS_DRAFT
            && $this->items()->exists();
    }

    /**
     * Check if quotation can be approved.
     */
    public function canApprove(): bool
    {
        return $this->status === self::STATUS_SUBMITTED
            && ! $this->isExpired();
    }

    /**
     * Check if quotation can be rejected.
     */
    public function canReject(): bool
    {
        return $this->status === self::STATUS_SUBMITTED;
    }

    /**
     * Check if quotation can be converted to invoice.
     */
    public function canConvert(): bool
    {
        return $this->status === self::STATUS_APPROVED
            && $this->converted_to_invoice_id === null;
    }

    /**
     * Check if quotation can be revised.
     */
    public function canRevise(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_EXPIRED,
        ]);
    }

    /**
     * Check if quotation is expired.
     */
    public function isExpired(): bool
    {
        if ($this->status === self::STATUS_EXPIRED) {
            return true;
        }

        return $this->valid_until->isPast();
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiry(): int
    {
        if ($this->valid_until->isPast()) {
            return 0;
        }

        return (int) now()->diffInDays($this->valid_until);
    }

    /**
     * Get the full quotation number with revision suffix.
     */
    public function getFullNumber(): string
    {
        if ($this->revision > 0) {
            return "{$this->quotation_number}-R{$this->revision}";
        }

        return $this->quotation_number;
    }

    /**
     * Get status label in Indonesian.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SUBMITTED => 'Diajukan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_EXPIRED => 'Kedaluwarsa',
            self::STATUS_CONVERTED => 'Dikonversi',
            default => $this->status,
        };
    }

    /**
     * Calculate and update totals from items.
     */
    public function calculateTotals(): void
    {
        $subtotal = 0;
        $taxAmount = 0;

        foreach ($this->items as $item) {
            $subtotal += $item->line_total;
            $taxAmount += $item->tax_amount;
        }

        $this->subtotal = $subtotal;

        // Apply header-level discount
        if ($this->discount_type === 'percentage' && $this->discount_value > 0) {
            $this->discount_amount = (int) round($subtotal * ($this->discount_value / 100));
        } elseif ($this->discount_type === 'fixed') {
            $this->discount_amount = (int) $this->discount_value;
        } else {
            $this->discount_amount = 0;
        }

        // Calculate tax on (subtotal - discount)
        $taxableAmount = $subtotal - $this->discount_amount;
        $this->tax_amount = (int) round($taxableAmount * ($this->tax_rate / 100));

        // Total
        $this->total = $taxableAmount + $this->tax_amount;

        // Base currency total
        if ($this->currency !== 'IDR' && $this->exchange_rate > 0) {
            $this->base_currency_total = (int) round($this->total * $this->exchange_rate);
        } else {
            $this->base_currency_total = $this->total;
        }
    }

    /**
     * Get default terms and conditions from config.
     */
    public static function getDefaultTermsConditions(string $locale = 'id'): string
    {
        $template = config("accounting.quotation.terms_conditions.{$locale}", '');
        $validityDays = config('accounting.quotation.default_validity_days', 30);

        return str_replace('{validity_days}', (string) $validityDays, $template);
    }

    /**
     * Generate the next quotation number.
     */
    public static function generateQuotationNumber(): string
    {
        $prefix = 'QUO-'.now()->format('Ym').'-';
        $lastQuotation = static::query()
            ->where('quotation_number', 'like', $prefix.'%')
            ->orderBy('quotation_number', 'desc')
            ->first();

        if ($lastQuotation) {
            $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get the next revision number for this quotation.
     */
    public function getNextRevisionNumber(): int
    {
        // If this is already a revision, find the original
        $originalId = $this->original_quotation_id ?? $this->id;

        $maxRevision = static::query()
            ->where(function ($query) use ($originalId) {
                $query->where('id', $originalId)
                    ->orWhere('original_quotation_id', $originalId);
            })
            ->max('revision');

        return ($maxRevision ?? 0) + 1;
    }
}
