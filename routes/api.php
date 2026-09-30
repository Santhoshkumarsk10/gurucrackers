<?php

use App\Http\Controllers\Api\OrderApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customer Mobile App REST APIs
|--------------------------------------------------------------------------
| Version: v1
*/

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    // 1. Initial Store Metadata (Shop info, banners, contact, UPI info)
    Route::get('/init', [OrderApiController::class, 'init'])->name('api.v1.init');

    // 2. Product Catalog (Categories with active products, rates, images)
    Route::get('/catalog', [OrderApiController::class, 'catalog'])->name('api.v1.catalog');

    // 3. Order Submission (Cart checkout with validation & duplicate prevention)
    Route::post('/orders', [OrderApiController::class, 'store'])->middleware('throttle:20,1')->name('api.v1.orders.store');

    // 4. Order Tracking (Lookup by order number or mobile number)
    Route::match(['get', 'post'], '/orders/track', [OrderApiController::class, 'track'])->middleware('throttle:30,1')->name('api.v1.orders.track');

    // 5. Single Order Details & Invoices
    Route::get('/orders/{orderNumber}', [OrderApiController::class, 'show'])->name('api.v1.orders.show');
});
