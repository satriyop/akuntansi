<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreQuotationRequest;
use App\Http\Requests\Api\V1\UpdateQuotationRequest;
use App\Http\Resources\Api\V1\InvoiceResource;
use App\Http\Resources\Api\V1\QuotationResource;
use App\Models\Accounting\Quotation;
use App\Services\Accounting\QuotationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use InvalidArgumentException;

class QuotationController extends Controller
{
    public function __construct(
        private QuotationService $quotationService
    ) {}

    /**
     * Display a listing of quotations.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Quotation::query()->with(['contact', 'items']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('contact_id')) {
            $query->where('contact_id', $request->input('contact_id'));
        }

        if ($request->has('start_date')) {
            $query->where('quotation_date', '>=', $request->input('start_date'));
        }

        if ($request->has('end_date')) {
            $query->where('quotation_date', '<=', $request->input('end_date'));
        }

        if ($request->boolean('expired_only')) {
            $query->expired();
        }

        if ($request->boolean('active_only')) {
            $query->active();
        }

        if ($request->has('search')) {
            $search = strtolower($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(quotation_number) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(subject) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(reference) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('contact', fn ($q) => $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]));
            });
        }

        $quotations = $query->orderByDesc('quotation_date')
            ->orderByDesc('id')
            ->paginate($request->input('per_page', 25));

        return QuotationResource::collection($quotations);
    }

    /**
     * Store a newly created quotation.
     */
    public function store(StoreQuotationRequest $request): JsonResponse
    {
        $quotation = $this->quotationService->create($request->validated());

        return (new QuotationResource($quotation))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified quotation.
     */
    public function show(Quotation $quotation): QuotationResource
    {
        return new QuotationResource(
            $quotation->load(['contact', 'items.product', 'revisions', 'convertedInvoice'])
        );
    }

    /**
     * Update the specified quotation.
     */
    public function update(UpdateQuotationRequest $request, Quotation $quotation): QuotationResource|JsonResponse
    {
        try {
            $quotation = $this->quotationService->update($quotation, $request->validated());

            return new QuotationResource($quotation);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove the specified quotation.
     */
    public function destroy(Quotation $quotation): JsonResponse
    {
        if (! $quotation->isEditable()) {
            return response()->json([
                'message' => 'Hanya penawaran draft yang dapat dihapus.',
            ], 422);
        }

        $quotation->delete();

        return response()->json(['message' => 'Penawaran berhasil dihapus.']);
    }

    /**
     * Submit quotation for approval.
     */
    public function submit(Quotation $quotation): QuotationResource|JsonResponse
    {
        try {
            $quotation = $this->quotationService->submit($quotation);

            return new QuotationResource($quotation);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Approve a quotation.
     */
    public function approve(Quotation $quotation): QuotationResource|JsonResponse
    {
        try {
            $quotation = $this->quotationService->approve($quotation);

            return new QuotationResource($quotation);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Reject a quotation.
     */
    public function reject(Request $request, Quotation $quotation): QuotationResource|JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ], [
            'reason.required' => 'Alasan penolakan harus diisi.',
        ]);

        try {
            $quotation = $this->quotationService->reject($quotation, $request->input('reason'));

            return new QuotationResource($quotation);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Create a revision of a quotation.
     */
    public function revise(Quotation $quotation): QuotationResource|JsonResponse
    {
        try {
            $newQuotation = $this->quotationService->revise($quotation);

            return (new QuotationResource($newQuotation))
                ->response()
                ->setStatusCode(201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Convert quotation to invoice.
     */
    public function convertToInvoice(Quotation $quotation): JsonResponse
    {
        try {
            $invoice = $this->quotationService->convertToInvoice($quotation);

            return response()->json([
                'message' => 'Penawaran berhasil dikonversi menjadi faktur.',
                'invoice' => new InvoiceResource($invoice),
                'quotation' => new QuotationResource($quotation->fresh(['contact', 'items'])),
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * Duplicate a quotation.
     */
    public function duplicate(Quotation $quotation): JsonResponse
    {
        $newQuotation = $this->quotationService->duplicate($quotation);

        return response()->json([
            'message' => 'Penawaran berhasil diduplikasi.',
            'data' => new QuotationResource($newQuotation),
        ], 201);
    }

    /**
     * Generate PDF (placeholder).
     */
    public function pdf(Quotation $quotation): JsonResponse
    {
        // Placeholder for PDF generation
        return response()->json([
            'message' => 'Fitur PDF belum tersedia.',
            'quotation_number' => $quotation->getFullNumber(),
        ], 501);
    }

    /**
     * Get quotation statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $statistics = $this->quotationService->getStatistics(
            $request->input('start_date'),
            $request->input('end_date')
        );

        return response()->json(['data' => $statistics]);
    }
}
