<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\RentalRequest;
use App\Services\ActivityService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Quotation::with('customer');

        if ($request->user()->role === 'customer') {
            $query->whereHas('customer', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        $quotations = $query
            ->when($request->search, fn ($q, $s) => $q->where('quotation_number', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'status']);

        return view('pages.quotations.index', compact('quotations', 'filters'));
    }

    public function create(Request $request, ?RentalRequest $rentalRequest = null): View
    {
        $requests = RentalRequest::with('customer', 'items.equipment')->whereIn('status', ['reviewed', 'quoted'])->latest()->get();
        $equipment = Equipment::with('category')->get();
        $selectedRequest = $rentalRequest ?? ($request->query('request') ? RentalRequest::find($request->query('request')) : null);

        return view('pages.quotations.create', compact('requests', 'equipment', 'selectedRequest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rental_request_id' => ['required', 'exists:rental_requests,id'],
            'valid_until' => ['required', 'date'],
            'rental_period_start' => ['required', 'date'],
            'rental_period_end' => ['required', 'date', 'after_or_equal:rental_period_start'],
            'equipment_id' => ['required', 'exists:equipment,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_rate' => ['required', 'numeric', 'min:0'],
            'operator_cost' => ['nullable', 'numeric', 'min:0'],
            'transportation_cost' => ['nullable', 'numeric', 'min:0'],
            'fuel_cost' => ['nullable', 'numeric', 'min:0'],
            'additional_service_cost' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $rentalRequest = RentalRequest::with('items')->findOrFail($validated['rental_request_id']);
        $durationDays = \Carbon\Carbon::parse($validated['rental_period_start'])->diffInDays(\Carbon\Carbon::parse($validated['rental_period_end'])) + 1;

        $equipment = Equipment::findOrFail($validated['equipment_id']);
        $lineTotal = $validated['unit_rate'] * $validated['quantity'] * $durationDays;

        $rentalRate = $lineTotal;
        $operatorCost = $validated['operator_cost'] ?? 0;
        $transportationCost = $validated['transportation_cost'] ?? 0;
        $fuelCost = $validated['fuel_cost'] ?? 0;
        $additionalCost = $validated['additional_service_cost'] ?? 0;
        $discount = $validated['discount'] ?? 0;

        $subtotal = $rentalRate + $operatorCost + $transportationCost + $fuelCost + $additionalCost - $discount;
        $taxRate = $validated['tax_rate'] ?? 11;
        $taxAmount = $subtotal * ($taxRate / 100);
        $grandTotal = $subtotal + $taxAmount;

        $nextQuoId = (int) Quotation::max('id') + 1;
        $quotationNumber = 'QUO-' . date('Y') . '-' . str_pad((string) $nextQuoId, 4, '0', STR_PAD_LEFT);
        while (Quotation::where('quotation_number', $quotationNumber)->exists()) {
            $nextQuoId++;
            $quotationNumber = 'QUO-' . date('Y') . '-' . str_pad((string) $nextQuoId, 4, '0', STR_PAD_LEFT);
        }

        $quotation = Quotation::create([
            'quotation_number' => $quotationNumber,
            'rental_request_id' => $rentalRequest->id,
            'customer_id' => $rentalRequest->customer_id,
            'project_id' => $rentalRequest->project_id,
            'valid_until' => $validated['valid_until'],
            'rental_period_start' => $validated['rental_period_start'],
            'rental_period_end' => $validated['rental_period_end'],
            'rental_rate' => $rentalRate,
            'operator_cost' => $operatorCost,
            'transportation_cost' => $transportationCost,
            'fuel_cost' => $fuelCost,
            'additional_service_cost' => $additionalCost,
            'discount' => $discount,
            'tax_rate' => $taxRate,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'status' => 'sent',
            'notes' => $validated['notes'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'equipment_id' => $equipment->id,
            'equipment_name_snapshot' => "{$equipment->brand} {$equipment->model} ({$equipment->equipment_code})",
            'quantity' => $validated['quantity'],
            'unit' => 'unit',
            'unit_rate' => $validated['unit_rate'],
            'duration_days' => $durationDays,
            'line_total' => $lineTotal,
        ]);

        $rentalRequest->update(['status' => 'quoted']);

        ActivityService::log('create', 'quotation', $quotation->id, "Quotation {$quotation->quotation_number} generated");

        if ($rentalRequest->customer->user_id) {
            NotificationService::send($rentalRequest->customer->user_id, 'Quotation Received', "Your quotation {$quotation->quotation_number} is ready for review.", 'success', route('customer.quotations.show', $quotation->id));
        }

        return redirect()->route('admin.quotations.show', $quotation->id)->with('success', 'Quotation generated successfully.');
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load('customer', 'items.equipment', 'rentalRequest', 'contract');

        return view('pages.quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation): View
    {
        $requests = RentalRequest::with('customer', 'items.equipment')->latest()->get();
        $equipment = Equipment::with('category')->get();

        return view('pages.quotations.edit', compact('quotation', 'requests', 'equipment'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $status = $request->validate(['status' => ['required', 'in:accepted,revision,rejected']])['status'];
        $quotation->update(['status' => $status]);

        ActivityService::log('update', 'quotation', $quotation->id, "Quotation {$quotation->quotation_number} {$status}");

        if ($status === 'accepted' && $quotation->rentalRequest) {
            $quotation->rentalRequest->update(['status' => 'approved']);
        }

        if ($quotation->customer->user_id) {
            NotificationService::send($quotation->customer->user_id, 'Quotation Updated', "Your quotation {$quotation->quotation_number} has been marked as {$status}.", 'info', route('customer.quotations.show', $quotation->id));
        }

        return back()->with('success', "Quotation marked as {$status}.");
    }

    public function generateContract(Quotation $quotation): RedirectResponse
    {
        $controller = app(ContractController::class);

        return $controller->createFromQuotation($quotation);
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        ActivityService::log('delete', 'quotation', $quotation->id, "Quotation {$quotation->quotation_number} removed");
        $quotation->delete();

        return redirect()->route('admin.quotations.index')->with('success', 'Quotation removed.');
    }
}
