<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\EquipmentImage;
use App\Services\ActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'brand', 'status', 'location']);
        $equipment = Equipment::with('category')
            ->filter($filters)
            ->paginate(15)
            ->withQueryString();

        $categories = EquipmentCategory::orderBy('sort_order')->get();
        $brands = Equipment::select('brand')->distinct()->orderBy('brand')->pluck('brand');

        return view('pages.equipment.index', compact('equipment', 'categories', 'brands', 'filters'));
    }

    public function create(): View
    {
        $categories = EquipmentCategory::orderBy('sort_order')->get();

        return view('pages.equipment.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['equipment_code'] = strtoupper($request->equipment_code);

        $equipment = Equipment::create($data);
        ActivityService::log('create', 'equipment', $equipment->id, "Equipment {$equipment->equipment_code} added to fleet");

        return redirect()->route('admin.equipment.show', $equipment->id)->with('success', 'Equipment added successfully.');
    }

    public function show(Equipment $equipment): View
    {
        $equipment->load('category', 'images', 'maintenanceRecords', 'assignedOperators');

        return view('pages.equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment): View
    {
        $categories = EquipmentCategory::orderBy('sort_order')->get();

        return view('pages.equipment.edit', compact('equipment', 'categories'));
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['equipment_code'] = strtoupper($request->equipment_code);

        $equipment->update($data);
        ActivityService::log('update', 'equipment', $equipment->id, "Equipment {$equipment->equipment_code} updated");

        return redirect()->route('admin.equipment.show', $equipment->id)->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        ActivityService::log('delete', 'equipment', $equipment->id, "Equipment {$equipment->equipment_code} removed");
        $equipment->delete();

        return redirect()->route('admin.equipment.index')->with('success', 'Equipment removed successfully.');
    }

    public function updateStatus(Request $request, Equipment $equipment): RedirectResponse
    {
        $request->validate(['status' => ['required', 'in:available,rented,maintenance,unavailable']]);
        $equipment->update(['status' => $request->status]);
        ActivityService::log('update', 'equipment', $equipment->id, "Equipment {$equipment->equipment_code} status changed to {$request->status}");

        return back()->with('success', 'Equipment status updated.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'equipment_code' => ['required', 'string', 'max:30'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:equipment_categories,id'],
            'brand' => ['required', 'string', 'max:100'],
            'model' => ['required', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:2000', 'max:' . (date('Y') + 1)],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'operating_weight' => ['nullable', 'numeric'],
            'engine_power' => ['nullable', 'numeric'],
            'bucket_capacity' => ['nullable', 'numeric'],
            'fuel_capacity' => ['nullable', 'numeric'],
            'operating_hours' => ['nullable', 'numeric'],
            'current_location' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'condition' => ['required', 'in:excellent,good,fair'],
            'status' => ['required', 'in:available,rented,maintenance,unavailable'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'weekly_rate' => ['nullable', 'numeric', 'min:0'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'deposit' => ['nullable', 'numeric', 'min:0'],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'next_service_hours' => ['nullable', 'numeric'],
            'hourly_rate' => ['nullable', 'numeric'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url'],
        ]);
    }
}
