<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'brand', 'capacity', 'location', 'status', 'sort']);

        $equipment = Equipment::with('category', 'images')
            ->filter($filters)
            ->paginate(12)
            ->withQueryString();

        $categories = EquipmentCategory::withCount('equipment')->get();
        $brands = Equipment::select('brand')->distinct()->orderBy('brand')->pluck('brand');
        $locations = Equipment::select('current_location')->distinct()->whereNotNull('current_location')->orderBy('current_location')->pluck('current_location');

        return view('pages.landing.equipment-catalog', compact('equipment', 'categories', 'brands', 'locations', 'filters'));
    }

    public function show($id): View
    {
        $equipment = Equipment::with('category', 'images', 'maintenanceRecords', 'assignedOperators')
            ->findOrFail($id);

        $similar = Equipment::where('category_id', $equipment->category_id)
            ->where('id', '!=', $equipment->id)
            ->with('category', 'images')
            ->take(4)->get();

        return view('pages.landing.equipment-detail', compact('equipment', 'similar'));
    }
}
