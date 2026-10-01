<?php

use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\BulkMessageController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Admin\WhatsAppController as AdminWhatsAppController;
use App\Http\Controllers\Admin\DatabaseManagerController as AdminDatabaseManagerController;
use App\Http\Controllers\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Public routes - this is what the QR code points to
 * |--------------------------------------------------------------------------
 */
Route::get('/', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])->middleware('throttle:60,1')->name('order.store');
Route::post('/order/send-otp', [OrderController::class, 'sendOrderOtp'])->middleware('throttle:20,1')->name('order.send_otp');
Route::post('/order/verify-otp', [OrderController::class, 'verifyOrderOtp'])->middleware('throttle:40,1')->name('order.verify_otp');
Route::get('/order/success/{orderNumber}', [OrderController::class, 'success'])->name('order.success');
Route::match(['get', 'post'], '/order/invoice/{orderNumber}', [OrderController::class, 'publicInvoice'])->middleware('throttle:30,1')->name('order.public_invoice');
Route::get('/order/invoice/{orderNumber}/download', [OrderController::class, 'downloadInvoice'])->middleware('throttle:30,1')->name('order.public_invoice.download');
Route::match(['get', 'post'], '/track-order', [OrderController::class, 'trackOrder'])->middleware('throttle:30,1')->name('order.track');

// QR code image for the order form URL — open this in browser to save/print it
Route::get('/order-qr', [OrderController::class, 'qrCode'])->name('order.qr');

// Free & 100% accurate India Post Pincode Lookup API
Route::get('/api/pincode/{pincode}', [OrderController::class, 'lookupPincode'])->middleware('throttle:60,1')->name('pincode.lookup');

// Internal WhatsApp microservice webhook for inbound customer messages
Route::post('/api/whatsapp/webhook', [AdminWhatsAppController::class, 'webhook'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

/*
 * |--------------------------------------------------------------------------
 * | Admin auth
 * |--------------------------------------------------------------------------
 */
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->middleware('throttle:5,1')->name('admin.login.submit');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Admin Password Reset via WhatsApp OTP
Route::get('/admin/forgot-password', [AdminForgotPasswordController::class, 'showForgotForm'])->name('admin.password.request');
Route::post('/admin/forgot-password', [AdminForgotPasswordController::class, 'sendOtp'])->middleware('throttle:6,1')->name('admin.password.email');
Route::get('/admin/forgot-password/verify', [AdminForgotPasswordController::class, 'showVerifyForm'])->name('admin.password.verify');
Route::post('/admin/forgot-password/reset', [AdminForgotPasswordController::class, 'resetPassword'])->middleware('throttle:10,1')->name('admin.password.update');

/*
 * |--------------------------------------------------------------------------
 * | Admin panel - protected
 * |--------------------------------------------------------------------------
 */
Route::middleware(['auth', 'throttle:120,1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Sales Reports & Data Exports
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/csv', [AdminReportController::class, 'exportCsv'])->name('reports.export_csv');
    Route::get('/reports/export/items-csv', [AdminReportController::class, 'exportItemsCsv'])->name('reports.export_items_csv');
    Route::get('/reports/export/pdf', [AdminReportController::class, 'exportPdf'])->name('reports.export_pdf');

    // Profile & Account
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/change-password', [AdminProfileController::class, 'updatePassword'])->name('change_password');

    // Notifications
    Route::get('/notifications/check', [AdminOrderController::class, 'checkNewOrders'])->name('notifications.check');
    Route::post('/notifications/mark-all-read', [AdminOrderController::class, 'markAllNotificationsRead'])->name('notifications.mark_all_read');
    Route::post('/notifications/{order}/mark-read', [AdminOrderController::class, 'markNotificationRead'])->name('notifications.mark_read');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/packing-checklist', [AdminOrderController::class, 'packingChecklist'])->name('orders.packing_checklist');
    Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/dispatch', [AdminOrderController::class, 'dispatchOrder'])->name('orders.dispatch');
    Route::post('/orders/{order}/send-text-invoice', [AdminOrderController::class, 'sendTextInvoice'])->name('orders.send_text_invoice');
    Route::post('/orders/{order}/send-packing-list', [AdminOrderController::class, 'sendPackingList'])->name('orders.send_packing_list');
    Route::post('/orders/{order}/send-invoice-pdf', [AdminOrderController::class, 'sendInvoicePdf'])->name('orders.send_invoice_pdf');
    Route::post('/orders/{order}/send-dispatch-lr', [AdminOrderController::class, 'sendDispatchLr'])->name('orders.send_dispatch_lr');

    Route::post('/categories/{id}/restore', [AdminCategoryController::class, 'restore'])->name('categories.restore');
    Route::delete('/categories/{id}/force-delete', [AdminCategoryController::class, 'forceDelete'])->name('categories.force_delete');
    Route::resource('categories', AdminCategoryController::class);

    Route::post('/products/bulk-upload', [AdminProductController::class, 'bulkUpload'])->name('products.bulk_upload');
    Route::get('/products/sample-template', [AdminProductController::class, 'downloadSampleTemplate'])->name('products.sample_template');
    Route::post('/products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{id}/force-delete', [AdminProductController::class, 'forceDelete'])->name('products.force_delete');
    Route::resource('products', AdminProductController::class);

    // Shop Details & Offers
    Route::get('/shop', [AdminShopController::class, 'edit'])->name('shop.edit');
    Route::put('/shop', [AdminShopController::class, 'update'])->name('shop.update');

    // Promotional Banners & Carousel
    Route::get('/banners', [AdminBannerController::class, 'index'])->name('banners.index');
    Route::post('/banners', [AdminBannerController::class, 'store'])->name('banners.store');
    Route::put('/banners/{banner}', [AdminBannerController::class, 'update'])->name('banners.update');
    Route::post('/banners/{banner}/toggle', [AdminBannerController::class, 'toggleActive'])->name('banners.toggle');
    Route::delete('/banners/{banner}', [AdminBannerController::class, 'destroy'])->name('banners.destroy');

    // Free Automated WhatsApp Gateway & Real-Time Live Chat
    Route::get('/whatsapp', [AdminWhatsAppController::class, 'index'])->name('whatsapp.index');
    Route::get('/whatsapp/status', [AdminWhatsAppController::class, 'status'])->name('whatsapp.status');
    Route::get('/whatsapp/chats', [AdminWhatsAppController::class, 'chats'])->name('whatsapp.chats');
    Route::get('/whatsapp/messages', [AdminWhatsAppController::class, 'messages'])->name('whatsapp.messages');
    Route::post('/whatsapp/chat/send', [AdminWhatsAppController::class, 'sendChat'])->name('whatsapp.chat.send');
    Route::post('/whatsapp/test', [AdminWhatsAppController::class, 'sendTest'])->name('whatsapp.test');
    Route::post('/whatsapp/logout', [AdminWhatsAppController::class, 'logout'])->name('whatsapp.logout');

    // 100% WhatsApp Audit Tracking Log & Data Export
    Route::get('/whatsapp/audit-log', [AdminWhatsAppController::class, 'auditLog'])->name('whatsapp.audit_log');
    Route::get('/whatsapp/audit-log/export', [AdminWhatsAppController::class, 'exportAuditLog'])->name('whatsapp.audit_log.export');

    // Bulk Messaging for Dispatched Customers
    Route::get('/bulk-messaging', [BulkMessageController::class, 'index'])->name('messaging.index');
    Route::post('/bulk-messaging/send', [BulkMessageController::class, 'sendBulk'])->name('messaging.send');
    Route::post('/bulk-messaging/{order}/send', [BulkMessageController::class, 'sendIndividual'])->name('messaging.send_individual');

    // 100% Application-Wide Audit Tracking System & History
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit_logs.index');
    Route::get('/audit-logs/export', [AdminAuditLogController::class, 'exportCsv'])->name('audit_logs.export');
    Route::get('/audit-logs/export/pdf', [AdminAuditLogController::class, 'exportPdf'])->name('audit_logs.export_pdf');
    Route::get('/audit-logs/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit_logs.show');

    // Database Manager — Backup, Clear Tables, Email Backup
    Route::get('/database', [AdminDatabaseManagerController::class, 'index'])->name('database.index');
    Route::post('/database/backup', [AdminDatabaseManagerController::class, 'backup'])->name('database.backup');
    Route::get('/database/backup/{filename}/download', [AdminDatabaseManagerController::class, 'downloadBackup'])->name('database.backup.download');
    Route::delete('/database/backup/{filename}', [AdminDatabaseManagerController::class, 'deleteBackup'])->name('database.backup.delete');
    Route::post('/database/email-backup', [AdminDatabaseManagerController::class, 'emailBackup'])->name('database.email_backup');
    Route::post('/database/clear-tables', [AdminDatabaseManagerController::class, 'clearTables'])->name('database.clear_tables');
    Route::post('/database/clear-all', [AdminDatabaseManagerController::class, 'clearAll'])->name('database.clear_all');
});
