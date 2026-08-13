<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Project;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function index(): View
    {
        $categories = EquipmentCategory::withCount('equipment')
            ->orderBy('sort_order')->get();

        $featured = Equipment::with('category', 'images')
            ->where('status', 'available')
            ->inRandomOrder()->take(6)->get();

        $solutions = [
            [
                'title' => 'Construction',
                'description' => 'Earthmoving, site preparation, and material handling equipment for residential and commercial building projects.',
                'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Mining',
                'description' => 'High-capacity hauling and excavation fleets built for continuous mining operations.',
                'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Infrastructure',
                'description' => 'Road, bridge, and utility equipment for long-duration public works programs.',
                'image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Plantation',
                'description' => 'Land clearing, hauling, and material handling solutions for estate operations.',
                'image' => 'https://images.unsplash.com/photo-1517581177682-a085bb7ffb15?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Energy',
                'description' => 'Rigging, lifting, and heavy transport equipment for power and energy projects.',
                'image' => 'https://images.unsplash.com/photo-1590486803833-1c95b0336202?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'Industrial',
                'description' => 'Material handling and logistics equipment for factory and warehouse operations.',
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        $projects = Project::with('customer')
            ->whereIn('status', ['active', 'completed'])
            ->latest()->take(4)->get();

        $stats = [
            'equipment' => Equipment::count(),
            'projects' => Project::whereIn('status', ['active', 'completed'])->count(),
            'categories' => EquipmentCategory::count(),
            'transactions' => \App\Models\Contract::count(),
        ];

        return view('pages.landing.index', compact('categories', 'featured', 'solutions', 'projects', 'stats'));
    }

    public function about(): View
    {
        $stats = [
            'equipment' => Equipment::count(),
            'projects' => Project::count(),
            'operators' => \App\Models\Operator::where('status', 'active')->count(),
            'customers' => \App\Models\Customer::where('status', 'active')->count(),
        ];

        return view('pages.landing.about', compact('stats'));
    }

    public function solutions(): View
    {
        return view('pages.landing.solutions');
    }

    public function services(): View
    {
        return view('pages.landing.services');
    }

    public function projects(): View
    {
        $projects = Project::with('customer')->latest()->paginate(9);

        return view('pages.landing.projects', compact('projects'));
    }

    public function contact(): View
    {
        return view('pages.landing.contact');
    }
}
