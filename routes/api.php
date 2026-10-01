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

    // 6. Mobile App WhatsApp OTP Verification
    Route::post('/orders/send-otp', [OrderApiController::class, 'sendOtp'])->middleware('throttle:10,1')->name('api.v1.orders.send_otp');
    Route::post('/orders/verify-otp', [OrderApiController::class, 'verifyOtp'])->middleware('throttle:20,1')->name('api.v1.orders.verify_otp');

    // 7. Pincode Lookup (Inside Tamil Nadu only)
    Route::get('/orders/pincode/{pincode}', [OrderApiController::class, 'lookupPincode'])->name('api.v1.orders.pincode');
});
