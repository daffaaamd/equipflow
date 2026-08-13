<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractItem;
use App\Models\Customer;
use App\Models\Equipment;
use App\Models\Project;
use App\Models\Quotation;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    public function index(Request $request): View
    {
        $query = Contract::with('customer');

        if ($request->user()->role === 'customer') {
            $query->whereHas('customer', fn ($q) => $q->where('user_id', $request->user()->id));
        }

        $contracts = $query
            ->when($request->search, fn ($q, $s) => $q->where('contract_number', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'status']);

        return view('pages.contracts.index', compact('contracts', 'filters'));
    }

    public function create(Request $request): View
    {
        $quotations = Quotation::with('customer', 'items')->where('status', 'accepted')->latest()->get();
        $equipment = Equipment::with('category')->get();
        $selectedQuotation = $request->query('quotation') ? Quotation::with('customer', 'items', 'rentalRequest')->find($request->query('quotation')) : null;

        return view('pages.contracts.create', compact('quotations', 'equipment', 'selectedQuotation'));
    }

    public function createFromQuotation(Quotation $quotation): RedirectResponse
    {
        return redirect()->route('admin.contracts.create', ['quotation' => $quotation->id]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'quotation_id' => ['required', 'exists:quotations,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'rental_rate' => ['required', 'numeric', 'min:0'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'payment_terms' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:draft,active'],
            'notes' => ['nullable', 'string'],
        ]);

        $quotation = Quotation::with('items')->findOrFail($validated['quotation_id']);

        $contract = Contract::create([
            'contract_number' => 'CON-' . date('Y') . '-' . str_pad((string) (Contract::count() + 1), 4, '0', STR_PAD_LEFT),
            'quotation_id' => $quotation->id,
            'customer_id' => $quotation->customer_id,
            'project_id' => $quotation->project_id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'rental_rate' => $validated['rental_rate'],
            'deposit' => $validated['deposit'] ?? 0,
            'payment_terms' => $validated['payment_terms'] ?? '30 days',
            'contract_value' => $quotation->grand_total,
            'status' => $validated['status'],
            'signed_at' => $validated['status'] === 'active' ? now() : null,
            'notes' => $validated['notes'],
        ]);

        $durationDays = \Carbon\Carbon::parse($validated['start_date'])->diffInDays(\Carbon\Carbon::parse($validated['end_date'])) + 1;

        foreach ($quotation->items as $item) {
            ContractItem::create([
                'contract_id' => $contract->id,
                'equipment_id' => $item->equipment_id,
                'quantity' => $item->quantity,
                'unit_rate' => $item->unit_rate,
                'duration_days' => $durationDays,
                'line_total' => $item->line_total,
            ]);

            if ($item->equipment_id && $validated['status'] === 'active') {
                $equipment = Equipment::find($item->equipment_id);
                if ($equipment) {
                    $equipment->update(['status' => 'rented']);
                }
            }
        }

        $quotation->update(['status' => 'accepted']);

        ActivityService::log('create', 'contract', $contract->id, "Contract {$contract->contract_number} created");

        return redirect()->route('admin.contracts.show', $contract->id)->with('success', 'Contract created successfully.');
    }

    public function show(Contract $contract): View
    {
        $contract->load('customer', 'items.equipment', 'quotation', 'invoices', 'deliveries');

        return view('pages.contracts.show', compact('contract'));
    }

    public function edit(Contract $contract): View
    {
        $quotations = Quotation::with('customer')->where('status', 'accepted')->latest()->get();

        return view('pages.contracts.edit', compact('contract', 'quotations'));
    }

    public function update(Request $request, Contract $contract): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,active,completed,terminated'],
            'notes' => ['nullable', 'string'],
        ]);

        $contract->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'],
            'signed_at' => $validated['status'] === 'active' && ! $contract->signed_at ? now() : $contract->signed_at,
        ]);

        ActivityService::log('update', 'contract', $contract->id, "Contract {$contract->contract_number} status set to {$validated['status']}");

        return back()->with('success', 'Contract updated.');
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        ActivityService::log('delete', 'contract', $contract->id, "Contract {$contract->contract_number} removed");
        $contract->delete();

        return redirect()->route('admin.contracts.index')->with('success', 'Contract removed.');
    }
}
