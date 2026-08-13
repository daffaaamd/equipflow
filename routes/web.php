<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MonitorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\Public\QuoteController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RentalRequestController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/about', [LandingController::class, 'about'])->name('about');
Route::get('/solutions', [LandingController::class, 'solutions'])->name('solutions');
Route::get('/services', [LandingController::class, 'services'])->name('services');
Route::get('/projects', [LandingController::class, 'projects'])->name('projects');
Route::get('/contact', [LandingController::class, 'contact'])->name('contact');

Route::get('/equipment', [CatalogController::class, 'index'])->name('catalog');
Route::get('/equipment/{id}', [CatalogController::class, 'show'])->name('catalog.show');

Route::get('/request-a-quote', [QuoteController::class, 'create'])->name('quote.create');
Route::post('/request-a-quote', [QuoteController::class, 'store'])->name('quote.store');
Route::get('/request-a-quote/thank-you', [QuoteController::class, 'thankYou'])->name('quote.thank-you');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.attempt');
});

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Shared Notifications (any authenticated user)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('/notifications/{notification}', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/unread/count', [NotificationController::class, 'apiUnreadCount'])->name('notifications.unread-count');
});

/*
|--------------------------------------------------------------------------
| Customer Portal
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer'])->prefix('portal')->name('customer.')->group(function () {
    Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard-alt');

    Route::get('/rental-requests', [RentalRequestController::class, 'index'])->name('rental-requests.index');
    Route::get('/rental-requests/create', [RentalRequestController::class, 'create'])->name('rental-requests.create');
    Route::post('/rental-requests', [RentalRequestController::class, 'store'])->name('rental-requests.store');
    Route::get('/rental-requests/{rental_request}', [RentalRequestController::class, 'show'])->name('rental-requests.show');

    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::put('/quotations/{quotation}/respond', [QuotationController::class, 'update'])->name('quotations.respond');

    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');

    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
});

/*
|--------------------------------------------------------------------------
| Management Portal (Admin, Sales, Operations, Maintenance, Finance)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,sales,operations,maintenance,finance'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Equipment
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('/equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
    Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::get('/equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
    Route::get('/equipment/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
    Route::post('/equipment/{equipment}/status', [EquipmentController::class, 'updateStatus'])->name('equipment.status');

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Rental Requests
    Route::get('/rental-requests', [RentalRequestController::class, 'index'])->name('rental-requests.index');
    Route::get('/rental-requests/create', [RentalRequestController::class, 'create'])->name('rental-requests.create');
    Route::post('/rental-requests', [RentalRequestController::class, 'store'])->name('rental-requests.store');
    Route::get('/rental-requests/{rental_request}', [RentalRequestController::class, 'show'])->name('rental-requests.show');
    Route::get('/rental-requests/{rental_request}/edit', [RentalRequestController::class, 'edit'])->name('rental-requests.edit');
    Route::put('/rental-requests/{rental_request}', [RentalRequestController::class, 'update'])->name('rental-requests.update');
    Route::delete('/rental-requests/{rental_request}', [RentalRequestController::class, 'destroy'])->name('rental-requests.destroy');

    // Quotations
    Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
    Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
    Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    Route::get('/quotations/{quotation}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/quotations/{quotation}/edit', [QuotationController::class, 'edit'])->name('quotations.edit');
    Route::put('/quotations/{quotation}', [QuotationController::class, 'update'])->name('quotations.update');
    Route::delete('/quotations/{quotation}', [QuotationController::class, 'destroy'])->name('quotations.destroy');
    Route::post('/quotations/{quotation}/contract', [QuotationController::class, 'generateContract'])->name('quotations.contract');

    // Contracts
    Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
    Route::get('/contracts/create', [ContractController::class, 'create'])->name('contracts.create');
    Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
    Route::get('/contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');
    Route::get('/contracts/{contract}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
    Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
    Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');

    // Deliveries
    Route::get('/deliveries', [DeliveryController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/create', [DeliveryController::class, 'create'])->name('deliveries.create');
    Route::post('/deliveries', [DeliveryController::class, 'store'])->name('deliveries.store');
    Route::get('/deliveries/{delivery}', [DeliveryController::class, 'show'])->name('deliveries.show');
    Route::get('/deliveries/{delivery}/edit', [DeliveryController::class, 'edit'])->name('deliveries.edit');
    Route::put('/deliveries/{delivery}', [DeliveryController::class, 'update'])->name('deliveries.update');
    Route::delete('/deliveries/{delivery}', [DeliveryController::class, 'destroy'])->name('deliveries.destroy');

    // Operators
    Route::get('/operators', [OperatorController::class, 'index'])->name('operators.index');
    Route::get('/operators/create', [OperatorController::class, 'create'])->name('operators.create');
    Route::post('/operators', [OperatorController::class, 'store'])->name('operators.store');
    Route::get('/operators/{operator}', [OperatorController::class, 'show'])->name('operators.show');
    Route::get('/operators/{operator}/edit', [OperatorController::class, 'edit'])->name('operators.edit');
    Route::put('/operators/{operator}', [OperatorController::class, 'update'])->name('operators.update');
    Route::delete('/operators/{operator}', [OperatorController::class, 'destroy'])->name('operators.destroy');

    // Maintenance
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('/maintenance/{maintenance_record}', [MaintenanceController::class, 'show'])->name('maintenance.show');
    Route::get('/maintenance/{maintenance_record}/edit', [MaintenanceController::class, 'edit'])->name('maintenance.edit');
    Route::put('/maintenance/{maintenance_record}', [MaintenanceController::class, 'update'])->name('maintenance.update');
    Route::delete('/maintenance/{maintenance_record}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');

    // Monitoring
    Route::get('/monitoring', [MonitorController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{equipment}', [MonitorController::class, 'show'])->name('monitoring.show');

    // Invoices & Payments
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

    // Finance dashboard
    Route::get('/finance', [FinanceController::class, 'dashboard'])->name('finance.dashboard');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'fleet'])->name('analytics.fleet');
    Route::get('/analytics/fleet', [AnalyticsController::class, 'fleet'])->name('analytics.fleet-alt');
    Route::get('/analytics/rental', [AnalyticsController::class, 'rental'])->name('analytics.rental');
    Route::get('/analytics/maintenance', [AnalyticsController::class, 'maintenance'])->name('analytics.maintenance');
    Route::get('/analytics/customer', [AnalyticsController::class, 'customer'])->name('analytics.customer');
    Route::get('/analytics/finance', [AnalyticsController::class, 'finance'])->name('analytics.finance');

    // Insights
    Route::get('/insights', [InsightsController::class, 'index'])->name('insights');

    // Reports
    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportsController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/export', [ReportsController::class, 'export'])->name('reports.export');

    // Audit
    Route::get('/audit', [AuditLogController::class, 'index'])->name('audit');
});

/*
|--------------------------------------------------------------------------
| Role-aware Dashboard Redirect
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (auth()->user()?->role === 'customer') {
        return redirect()->route('customer.dashboard');
    }

    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Static Asset Fallback (ensures images serve in all environments)
|--------------------------------------------------------------------------
*/
Route::get('/build/{path}', function ($path) {
    $possibleFiles = [
        public_path('build/' . $path),
        base_path('dist/build/' . $path),
        base_path('public/build/' . $path),
    ];

    $file = null;
    foreach ($possibleFiles as $f) {
        if (file_exists($f) && !is_dir($f)) {
            $file = $f;
            break;
        }
    }

    if (!$file) {
        abort(404);
    }

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'application/javascript; charset=utf-8',
        'json'  => 'application/json',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'svg'   => 'image/svg+xml',
    ];

    $mime = $mimes[$ext] ?? mime_content_type($file) ?: 'application/octet-stream';

    return response()->file($file, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=31536000, immutable',
    ]);
})->where('path', '.*');

Route::get('/img/{path}', function ($path) {
    $possibleFiles = [
        public_path('img/' . $path),
        base_path('dist/img/' . $path),
        base_path('public/img/' . $path),
    ];

    $file = null;
    foreach ($possibleFiles as $f) {
        if (file_exists($f) && !is_dir($f)) {
            $file = $f;
            break;
        }
    }

    if (!$file) {
        abort(404);
    }

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mimes = [
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
        'ico'  => 'image/x-icon',
    ];

    $mime = $mimes[$ext] ?? mime_content_type($file) ?: 'application/octet-stream';

    return response()->file($file, [
        'Content-Type' => $mime,
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');

Route::get('/js/{path}', function ($path) {
    $possibleFiles = [
        public_path('js/' . $path),
        base_path('dist/js/' . $path),
        base_path('public/js/' . $path),
    ];

    $file = null;
    foreach ($possibleFiles as $f) {
        if (file_exists($f) && !is_dir($f)) {
            $file = $f;
            break;
        }
    }

    if (!$file) {
        abort(404);
    }

    return response()->file($file, [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');


