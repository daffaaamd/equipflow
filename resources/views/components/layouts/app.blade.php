<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="notif-url" content="{{ route('notifications.unread-count') }}">
    <title>{{ $title ?? 'Dashboard' }} — EquipFlow</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="/js/chart.umd.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('head')
</head>
<body class="bg-charcoal-100">

    @php
        $user = auth()->user();
        $isCustomer = $user->role === 'customer';
        $role = $user->role;

        $canManage = in_array($role, ['admin', 'sales', 'operations']);
        $canFleet = in_array($role, ['admin', 'operations', 'maintenance']);
        $canCommercial = in_array($role, ['admin', 'sales']);
        $canFinance = in_array($role, ['admin', 'finance']);
        $canAnalytics = in_array($role, ['admin', 'sales']);

        $icons = [
            'grid' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
            'monitor' => '<rect x="2" y="4" width="20" height="13" rx="1"/><path d="M8 21h8M12 17v4"/>',
            'equipment' => '<path d="M7.5 3.5l9 2.25v12.5l-9 2.25V3.5z"/><path d="M7.5 7.5h9M7.5 12.5h9M7.5 17h9M16.5 6v12M7.5 6v12"/>',
            'operator' => '<circle cx="12" cy="8" r="4"/><path d="M5 21c.6-3.6 3.6-6 7-6s6.4 2.4 7 6"/>',
            'wrench' => '<path d="M14.7 6.3a4.5 4.5 0 00-6.1 6.1L3 18l3 3 5.6-5.6a4.5 4.5 0 006.1-6.1L14 13l-3-3 3.7-3.7z"/>',
            'doc' => '<path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9l-6-6z"/><path d="M14 3v6h6M9 13h6M9 17h6"/>',
            'send' => '<path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>',
            'handshake' => '<path d="M11 17l2 2a1 1 0 002 0l3-3a1 1 0 000-1.4l-4-4a1 1 0 00-1.4 0l-3 3a1 1 0 000 1.4L11 17z"/><path d="M12 4c1.5-1 3.5-1 5 .5M7 10l-3 3a1 1 0 000 1.4l3 3 2-2"/>',
            'truck' => '<path d="M1 4h13v12H1z"/><path d="M14 8h4l3 4v4h-7z"/><circle cx="6" cy="18" r="2"/><circle cx="17.5" cy="18" r="2"/>',
            'users' => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20a6.5 6.5 0 0113 0"/><path d="M16 4.5a3.5 3.5 0 010 7M19 14a5 5 0 012.5 6"/>',
            'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="1"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2M3 12h18"/>',
            'banknotes' => '<rect x="2" y="6" width="20" height="12" rx="1"/><circle cx="12" cy="12" r="2.5"/><path d="M6 10h.01M18 14h.01"/>',
            'invoice' => '<rect x="5" y="3" width="14" height="18" rx="1"/><path d="M9 8h6M9 12h6M9 16h4"/>',
            'card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>',
            'chart' => '<path d="M3 3v18h18"/><path d="M7 15v-4M12 15V7M17 15v-6"/>',
            'bulb' => '<path d="M9 18h6M10 21h4M12 3a6 6 0 00-4 10.5c.8.7 1 1.5 1 2.5h6c0-1 .2-1.8 1-2.5A6 6 0 0012 3z"/>',
            'report' => '<path d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V9l-6-6z"/><path d="M14 3v6h6"/><path d="M8 14h8M8 17h5"/>',
            'shield' => '<path d="M12 3l8 3v6c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/>',
            'bell' => '<path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M10 21a2 2 0 004 0"/>',
        ];

        $nav = [];
        $dashboardGroup = [];

        if ($isCustomer) {
            $dashboardGroup[] = ['label' => 'Dashboard', 'url' => route('customer.dashboard'), 'icon' => $icons['grid'], 'active' => request()->routeIs('customer.dashboard*')];
            $dashboardGroup[] = ['label' => 'Rental Requests', 'url' => route('customer.rental-requests.index'), 'icon' => $icons['doc'], 'active' => request()->routeIs('customer.rental-requests*')];
            $dashboardGroup[] = ['label' => 'Quotations', 'url' => route('customer.quotations.index'), 'icon' => $icons['send'], 'active' => request()->routeIs('customer.quotations*')];
            $dashboardGroup[] = ['label' => 'Contracts', 'url' => route('customer.contracts.index'), 'icon' => $icons['handshake'], 'active' => request()->routeIs('customer.contracts*')];
            $dashboardGroup[] = ['label' => 'Invoices', 'url' => route('customer.invoices.index'), 'icon' => $icons['invoice'], 'active' => request()->routeIs('customer.invoices*')];
        } else {
            $dashboardGroup[] = ['label' => 'Dashboard', 'url' => route('admin.dashboard'), 'icon' => $icons['grid'], 'active' => request()->routeIs('admin.dashboard')];
        }
        $dashboardGroup[] = ['label' => 'Notifications', 'url' => route('notifications'), 'icon' => $icons['bell'], 'active' => request()->routeIs('notifications')];
        $nav[] = ['label' => 'Overview', 'items' => $dashboardGroup];

        if (! $isCustomer) {
            $fleetItems = [];
            $fleetItems[] = ['label' => 'Fleet Monitoring', 'url' => route('admin.monitoring.index'), 'icon' => $icons['monitor'], 'active' => request()->routeIs('admin.monitoring*')];
            if ($canFleet) {
                $fleetItems[] = ['label' => 'Equipment', 'url' => route('admin.equipment.index'), 'icon' => $icons['equipment'], 'active' => request()->routeIs('admin.equipment*')];
                $fleetItems[] = ['label' => 'Operators', 'url' => route('admin.operators.index'), 'icon' => $icons['operator'], 'active' => request()->routeIs('admin.operators*')];
                $fleetItems[] = ['label' => 'Maintenance', 'url' => route('admin.maintenance.index'), 'icon' => $icons['wrench'], 'active' => request()->routeIs('admin.maintenance*')];
            }
            $nav[] = ['label' => 'Fleet', 'items' => $fleetItems];

            $rentalItems = [];
            if ($canManage) {
                $rentalItems[] = ['label' => 'Rental Requests', 'url' => route('admin.rental-requests.index'), 'icon' => $icons['doc'], 'active' => request()->routeIs('admin.rental-requests*')];
                $rentalItems[] = ['label' => 'Quotations', 'url' => route('admin.quotations.index'), 'icon' => $icons['send'], 'active' => request()->routeIs('admin.quotations*')];
                $rentalItems[] = ['label' => 'Contracts', 'url' => route('admin.contracts.index'), 'icon' => $icons['handshake'], 'active' => request()->routeIs('admin.contracts*')];
                $rentalItems[] = ['label' => 'Deliveries', 'url' => route('admin.deliveries.index'), 'icon' => $icons['truck'], 'active' => request()->routeIs('admin.deliveries*')];
            }
            $nav[] = ['label' => 'Rental', 'items' => $rentalItems];

            $commercialItems = [];
            if ($canCommercial) {
                $commercialItems[] = ['label' => 'Customers', 'url' => route('admin.customers.index'), 'icon' => $icons['users'], 'active' => request()->routeIs('admin.customers*')];
                $commercialItems[] = ['label' => 'Projects', 'url' => route('admin.projects.index'), 'icon' => $icons['briefcase'], 'active' => request()->routeIs('admin.projects*')];
            }
            $nav[] = ['label' => 'Commercial', 'items' => $commercialItems];

            $financeItems = [];
            if ($canFinance) {
                $financeItems[] = ['label' => 'Finance Dashboard', 'url' => route('admin.finance.dashboard'), 'icon' => $icons['banknotes'], 'active' => request()->routeIs('admin.finance.dashboard')];
                $financeItems[] = ['label' => 'Invoices', 'url' => route('admin.invoices.index'), 'icon' => $icons['invoice'], 'active' => request()->routeIs('admin.invoices*')];
                $financeItems[] = ['label' => 'Payments', 'url' => route('admin.payments.index'), 'icon' => $icons['card'], 'active' => request()->routeIs('admin.payments*')];
            }
            $nav[] = ['label' => 'Finance', 'items' => $financeItems];

            $analyticsItems = [];
            if ($canAnalytics) {
                $analyticsItems[] = ['label' => 'Fleet Analytics', 'url' => route('admin.analytics.fleet'), 'icon' => $icons['chart'], 'active' => request()->routeIs('admin.analytics.fleet*')];
                $analyticsItems[] = ['label' => 'Rental Analytics', 'url' => route('admin.analytics.rental'), 'icon' => $icons['chart'], 'active' => request()->routeIs('admin.analytics.rental*')];
                $analyticsItems[] = ['label' => 'Maintenance Analytics', 'url' => route('admin.analytics.maintenance'), 'icon' => $icons['chart'], 'active' => request()->routeIs('admin.analytics.maintenance*')];
                $analyticsItems[] = ['label' => 'Customer Analytics', 'url' => route('admin.analytics.customer'), 'icon' => $icons['chart'], 'active' => request()->routeIs('admin.analytics.customer*')];
                $analyticsItems[] = ['label' => 'Finance Analytics', 'url' => route('admin.analytics.finance'), 'icon' => $icons['chart'], 'active' => request()->routeIs('admin.analytics.finance*')];
                $analyticsItems[] = ['label' => 'Business Insights', 'url' => route('admin.insights'), 'icon' => $icons['bulb'], 'active' => request()->routeIs('admin.insights')];
            }
            $nav[] = ['label' => 'Analytics', 'items' => $analyticsItems];

            $governanceItems = [];
            if ($role === 'admin') {
                $governanceItems[] = ['label' => 'Reports', 'url' => route('admin.reports.index'), 'icon' => $icons['report'], 'active' => request()->routeIs('admin.reports*')];
                $governanceItems[] = ['label' => 'Audit Trail', 'url' => route('admin.audit'), 'icon' => $icons['shield'], 'active' => request()->routeIs('admin.audit')];
            }
            $nav[] = ['label' => 'Governance', 'items' => $governanceItems];
        }
    @endphp

    {{-- Sidebar --}}
    <x-sidebar :groups="$nav" :is-customer="$isCustomer" />

    <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-charcoal-950/50 lg:hidden"></div>

    {{-- Main --}}
    <div class="flex min-h-screen flex-col lg:pl-64">
        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-charcoal-200 bg-white px-4 sm:px-6">
            <button id="sidebar-open" type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-sm text-charcoal-500 hover:text-navy-900 lg:hidden">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="truncate font-display text-xl font-bold uppercase tracking-wide text-navy-900">{{ $title ?? 'Dashboard' }}</h1>
                <p class="hidden text-xs text-charcoal-500 sm:block">{{ $subtitle ?? ($isCustomer ? 'Customer Portal' : 'Operations Management Platform') }}</p>
            </div>

            <span class="hidden items-center gap-1.5 rounded-sm border border-charcoal-200 bg-charcoal-50 px-2 py-1 text-[10px] font-semibold uppercase tracking-widest text-charcoal-500 md:inline-flex">
                <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span> Demo Data
            </span>

            {{-- Notifications bell --}}
            <a href="{{ route('notifications') }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-sm text-charcoal-500 hover:bg-charcoal-100 hover:text-navy-900">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9" /><path stroke-linecap="round" stroke-linejoin="round" d="M10 21a2 2 0 004 0" /></svg>
                <span id="notif-badge" class="absolute right-1 top-1 hidden h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white"></span>
            </a>

            <div class="hidden h-8 w-px bg-charcoal-200 sm:block"></div>

            <div x-data="{ open: false }" class="relative">
                <button type="button" x-on:click="open = !open" class="flex items-center gap-2.5 rounded-sm p-1.5 hover:bg-charcoal-100">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-navy-900 font-display text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    <span class="hidden text-left sm:block">
                        <span class="block max-w-[10rem] truncate text-sm font-semibold text-charcoal-800">{{ $user->name }}</span>
                        <span class="block text-xs text-charcoal-500">{{ $user->roleLabel() }}</span>
                    </span>
                    <svg class="h-4 w-4 text-charcoal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </button>
                <div x-show="open" x-transition x-cloak x-on:click.outside="open = false" class="absolute right-0 z-30 mt-2 w-56 border border-charcoal-200 bg-white py-1 shadow-lg">
                    <a href="{{ route('landing') }}" class="block px-4 py-2 text-sm text-charcoal-700 hover:bg-charcoal-100">View Website</a>
                    @if (!$isCustomer && $canAnalytics)
                        <a href="{{ route('admin.analytics.fleet') }}" class="block px-4 py-2 text-sm text-charcoal-700 hover:bg-charcoal-100">Analytics</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Flash --}}
        <div class="px-4 pt-4 sm:px-6">
            <x-alert type="success" />
            <x-alert type="error" />
        </div>

        <main class="flex-1 px-4 py-6 sm:px-6">
            {{ $slot }}
        </main>

        <footer class="border-t border-charcoal-200 px-6 py-4 text-center text-xs text-charcoal-500">
            © {{ date('Y') }} EquipFlow &nbsp;·&nbsp; Buatan Daffa Ahmad Baihaqi
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
