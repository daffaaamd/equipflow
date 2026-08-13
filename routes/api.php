<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| RESTful endpoints used by AJAX / Fetch API within the application.
*/

Route::middleware('auth')->prefix('v1')->group(function () {
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'apiUnreadCount'])->name('api.notifications.unread-count');
});
