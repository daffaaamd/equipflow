<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\RentalRequest;
use App\Models\RentalRequestItem;
use App\Services\ActivityService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalRequestController extends Controller
{
    public function index(Request $request): View
    {
        $query = RentalRequest::with('customer');

        $user = $request->user();

        if ($user->role === 'customer') {
            $query->whereHas('customer', fn ($q) => $q->where('user_id', $user->id));
        }

        $requests = $query
            ->when($request->search, fn ($q, $s) => $q->where('request_number', 'like', "%{$s}%"))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only(['search', 'status']);

        return view('pages.rental-requests.index', compact('requests', 'filters'));
    }

    public function create(): View
    {
        $categories = EquipmentCategory::orderBy('sort_order')->get();
        $equipment = Equipment::available()->with('category')->get();
        $customer = auth()->user()->customer;

        return view('pages.rental-requests.create', compact('categories', 'equipment', 'customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_name' => ['required', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'project_location' => ['nullable', 'string', 'max:255'],
            'equipment_id' => ['nullable', 'exists:equipment,id'],
            'category_id' => ['nullable', 'exists:equipment_categories,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'operator_required' => ['nullable', 'boolean'],
            'transportation_included' => ['nullable', 'boolean'],
            'fuel_included' => ['nullable', 'boolean'],
            'additional_requirements' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $customer = $user->customer ?? Customer::create([
            'customer_code' => 'CUS-' . str_pad((string) (Customer::max('id') + 51), 4, '0', STR_PAD_LEFT),
            'user_id' => $user->id,
            'company_name' => $user->company_name ?? $user->name,
            'contact_person' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'status' => 'active',
        ]);

        $nextReqId = (int) RentalRequest::max('id') + 1;
        $requestNumber = 'REQ-' . date('Y') . '-' . str_pad((string) $nextReqId, 4, '0', STR_PAD_LEFT);
        while (RentalRequest::where('request_number', $requestNumber)->exists()) {
            $nextReqId++;
            $requestNumber = 'REQ-' . date('Y') . '-' . str_pad((string) $nextReqId, 4, '0', STR_PAD_LEFT);
        }

        $rentalRequest = RentalRequest::create([
            'request_number' => $requestNumber,
            'customer_id' => $customer->id,
            'contact_person' => $user->name,
            'contact_phone' => $user->phone,
            'project_name' => $validated['project_name'],
            'project_type' => $validated['project_type'] ?? null,
            'project_location' => $validated['project_location'] ?? null,
            'operator_required' => $validated['operator_required'] ?? false,
            'transportation_included' => $validated['transportation_included'] ?? false,
            'fuel_included' => $validated['fuel_included'] ?? false,
            'additional_requirements' => $validated['additional_requirements'] ?? null,
            'status' => 'pending',
        ]);

        RentalRequestItem::create([
            'rental_request_id' => $rentalRequest->id,
            'equipment_id' => $validated['equipment_id'] ?? null,
            'equipment_category_id' => $validated['category_id'] ?? null,
            'quantity' => $validated['quantity'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
        ]);

        ActivityService::log('create', 'rental_request', $rentalRequest->id, "Rental request {$rentalRequest->request_number} submitted");

        foreach (\App\Models\User::whereIn('role', ['admin', 'sales'])->get() as $admin) {
            NotificationService::send($admin->id, 'New Rental Request', "New request from {$customer->company_name}.", 'success', route('admin.rental-requests.show', $rentalRequest->id));
        }

        $showRoute = $user->role === 'customer' ? 'customer.rental-requests.show' : 'admin.rental-requests.show';

        return redirect()->route($showRoute, $rentalRequest->id)->with('success', 'Rental request submitted successfully. Our team will review it shortly.');
    }

    public function show(RentalRequest $rentalRequest): View
    {
        $rentalRequest->load('customer', 'items.equipment.category', 'items.category', 'quotation');

        return view('pages.rental-requests.show', compact('rentalRequest'));
    }

    public function edit(RentalRequest $rentalRequest): View
    {
        $categories = EquipmentCategory::orderBy('sort_order')->get();
        $equipment = Equipment::with('category')->get();

        return view('pages.rental-requests.edit', compact('rentalRequest', 'categories', 'equipment'));
    }

    public function update(Request $request, RentalRequest $rentalRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,reviewed,quoted,approved,rejected,cancelled'],
        ]);

        $rentalRequest->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        ActivityService::log('update', 'rental_request', $rentalRequest->id, "Request {$rentalRequest->request_number} marked as {$validated['status']}");

        if ($rentalRequest->customer->user_id) {
            NotificationService::send($rentalRequest->customer->user_id, 'Request Status Update', "Your request {$rentalRequest->request_number} is now {$validated['status']}.", 'info', route('customer.rental-requests.show', $rentalRequest->id));
        }

        return back()->with('success', 'Request status updated.');
    }

    public function destroy(RentalRequest $rentalRequest): RedirectResponse
    {
        ActivityService::log('delete', 'rental_request', $rentalRequest->id, "Request {$rentalRequest->request_number} removed");
        $rentalRequest->delete();

        $indexRoute = auth()->user()->role === 'customer' ? 'customer.rental-requests.index' : 'admin.rental-requests.index';

        return redirect()->route($indexRoute)->with('success', 'Request removed.');
    }
}
