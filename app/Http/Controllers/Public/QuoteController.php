<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\EquipmentCategory;
use App\Models\RentalRequest;
use App\Models\RentalRequestItem;
use App\Services\ActivityService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function create(Request $request): View
    {
        $categories = EquipmentCategory::orderBy('sort_order')->get();
        $equipment = \App\Models\Equipment::available()->with('category')->orderBy('equipment_code')->get();
        $selectedEquipment = $request->query('equipment');
        $customer = Auth::check() && Auth::user()->customer ? Auth::user()->customer : null;

        return view('pages.landing.request-quote', compact('categories', 'equipment', 'selectedEquipment', 'customer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'project_name' => ['required', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'project_location' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:equipment_categories,id'],
            'equipment_id' => ['nullable', 'exists:equipment,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'operator_required' => ['nullable', 'boolean'],
            'transportation_included' => ['nullable', 'boolean'],
            'fuel_included' => ['nullable', 'boolean'],
            'additional_requirements' => ['nullable', 'string', 'max:2000'],
        ]);

        $customer = Auth::check() && Auth::user()->customer
            ? Auth::user()->customer
            : Customer::where('email', $validated['email'])->first();

        if (! $customer) {
            $nextId = (int) Customer::max('id') + 1;
            $code = 'CUS-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
            while (Customer::where('customer_code', $code)->exists()) {
                $nextId++;
                $code = 'CUS-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
            }

            $customer = Customer::create([
                'customer_code' => $code,
                'company_name' => $validated['company_name'],
                'contact_person' => $validated['contact_person'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
            ]);
        }

        $nextReqId = (int) RentalRequest::max('id') + 1;
        $requestNumber = 'REQ-' . date('Y') . '-' . str_pad((string) $nextReqId, 4, '0', STR_PAD_LEFT);
        while (RentalRequest::where('request_number', $requestNumber)->exists()) {
            $nextReqId++;
            $requestNumber = 'REQ-' . date('Y') . '-' . str_pad((string) $nextReqId, 4, '0', STR_PAD_LEFT);
        }

        $rentalRequest = RentalRequest::create([
            'request_number' => $requestNumber,
            'customer_id' => $customer->id,
            'contact_person' => $validated['contact_person'],
            'contact_phone' => $validated['phone'] ?? null,
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

        ActivityService::log('create', 'rental_request', $rentalRequest->id, "New rental request {$requestNumber} submitted");

        $admins = \App\Models\User::whereIn('role', ['admin', 'sales'])->get();
        foreach ($admins as $admin) {
            NotificationService::send($admin->id, 'New Rental Request', "New request {$requestNumber} from {$customer->company_name}.", 'success', route('admin.rental-requests.show', $rentalRequest->id));
        }

        return redirect()->route('quote.thank-you')->with('request_number', $requestNumber);
    }

    public function thankYou(): View
    {
        $requestNumber = session('request_number');

        return view('pages.landing.quote-thank-you', compact('requestNumber'));
    }
}
