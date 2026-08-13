<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Equipment;
use App\Models\Project;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DeliveryController extends Controller
{
    public function index(Request $request): View
    {
        $deliveries = Delivery::with('equipment', 'customer', 'project')
            ->when($request->search, fn ($q, $s) => $q->where('delivery_number', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'status']);

        return view('pages.deliveries.index', compact('deliveries', 'filters'));
    }

    public function create(): View
    {
        $contracts = Contract::whereIn('status', ['draft', 'active'])->with('customer', 'items.equipment')->latest()->get();
        $equipment = Equipment::with('category')->get();

        return view('pages.deliveries.create', compact('contracts', 'equipment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_id' => ['required', 'exists:contracts,id'],
            'equipment_id' => ['required', 'exists:equipment,id'],
            'pickup_location' => ['nullable', 'string', 'max:255'],
            'destination' => ['nullable', 'string', 'max:255'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:30'],
            'transport_vehicle' => ['nullable', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:20'],
            'delivery_date' => ['required', 'date'],
            'estimated_arrival' => ['nullable', 'date'],
            'status' => ['required', 'in:scheduled,preparing,in_transit,delivered,confirmed'],
            'notes' => ['nullable', 'string'],
        ]);

        $contract = Contract::findOrFail($validated['contract_id']);

        $delivery = Delivery::create(array_merge($validated, [
            'delivery_number' => 'DLV-' . date('Y') . '-' . str_pad((string) (Delivery::count() + 1), 4, '0', STR_PAD_LEFT),
            'customer_id' => $contract->customer_id,
            'project_id' => $contract->project_id,
        ]));

        ActivityService::log('create', 'delivery', $delivery->id, "Delivery {$delivery->delivery_number} scheduled");

        return redirect()->route('admin.deliveries.show', $delivery->id)->with('success', 'Delivery scheduled successfully.');
    }

    public function show(Delivery $delivery): View
    {
        $delivery->load('contract', 'equipment', 'customer', 'project');

        return view('pages.deliveries.show', compact('delivery'));
    }

    public function edit(Delivery $delivery): View
    {
        $contracts = Contract::whereIn('status', ['draft', 'active'])->with('customer')->latest()->get();
        $equipment = Equipment::with('category')->get();

        return view('pages.deliveries.edit', compact('delivery', 'contracts', 'equipment'));
    }

    public function update(Request $request, Delivery $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'driver_name' => ['nullable', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:30'],
            'transport_vehicle' => ['nullable', 'string', 'max:255'],
            'plate_number' => ['nullable', 'string', 'max:20'],
            'estimated_arrival' => ['nullable', 'date'],
            'status' => ['required', 'in:scheduled,preparing,in_transit,delivered,confirmed'],
            'notes' => ['nullable', 'string'],
        ]);

        $delivery->update($validated);
        ActivityService::log('update', 'delivery', $delivery->id, "Delivery {$delivery->delivery_number} updated to {$validated['status']}");

        return back()->with('success', 'Delivery updated successfully.');
    }

    public function destroy(Delivery $delivery): RedirectResponse
    {
        ActivityService::log('delete', 'delivery', $delivery->id, "Delivery {$delivery->delivery_number} removed");
        $delivery->delete();

        return redirect()->route('admin.deliveries.index')->with('success', 'Delivery removed.');
    }
}
