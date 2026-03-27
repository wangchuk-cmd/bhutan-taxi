<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/trip/{id}', [HomeController::class, 'tripDetails'])->name('trip.details');

// API Route for real-time trip updates
Route::get('/api/trips/search', [HomeController::class, 'apiSearchTrips'])->name('api.trips.search');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/driver/register', [AuthController::class, 'showDriverRegister'])->name('driver.register');
    Route::post('/driver/register', [AuthController::class, 'driverRegister']);

    // Password Reset (User)
    Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

    // Google OAuth Routes
    Route::get('auth/google', [App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Passenger Routes (Authenticated)
Route::middleware('auth')->group(function () {
    // Bookings
    Route::get('/booking/create/{tripId}', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('bookings.my');
    Route::get('/booking/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/booking/{id}/receipt', [BookingController::class, 'receipt'])->name('booking.receipt');
    Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    // Payments
    Route::get('/payment/{bookingId}', [PaymentController::class, 'process'])->name('payment.process');
    Route::post('/payment/{bookingId}/complete', [PaymentController::class, 'complete'])->name('payment.complete');
    Route::post('/payment/{bookingId}/timeout', [PaymentController::class, 'timeout'])->name('payment.timeout');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');

    // Complaints/Feedback
    Route::get('/feedback', [NotificationController::class, 'createComplaint'])->name('feedback');
    Route::post('/feedback', [NotificationController::class, 'storeComplaint'])->name('feedback.store');
});

// Driver Routes
Route::prefix('driver')->middleware(['auth', 'driver'])->group(function () {
    Route::get('/dashboard', [DriverController::class, 'dashboard'])->name('driver.dashboard');
    Route::get('/trips', [DriverController::class, 'trips'])->name('driver.trips');
    Route::get('/trips/create', [DriverController::class, 'createTrip'])->name('driver.trips.create');
    Route::post('/trips', [DriverController::class, 'storeTrip'])->name('driver.trips.store');
    Route::get('/trips/{id}/edit', [DriverController::class, 'editTrip'])->name('driver.trips.edit');
    Route::put('/trips/{id}', [DriverController::class, 'updateTrip'])->name('driver.trips.update');
    Route::post('/trips/{id}/cancel', [DriverController::class, 'cancelTrip'])->name('driver.trips.cancel');
    Route::get('/trips/{id}/passengers', [DriverController::class, 'passengers'])->name('driver.passengers');
    Route::get('/payouts', [DriverController::class, 'payouts'])->name('driver.payouts');
    Route::get('/profile', [DriverController::class, 'profile'])->name('driver.profile');
    Route::put('/profile', [DriverController::class, 'updateProfile'])->name('driver.profile.update');
    
    // Driver Feedback
    Route::get('/feedback', [DriverController::class, 'createFeedback'])->name('driver.feedback');
    Route::post('/feedback', [DriverController::class, 'storeFeedback'])->name('driver.feedback.store');
});

// Admin Routes
// Admin Authentication Routes
Route::prefix('admin')->middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [App\Http\Controllers\AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::get('/register', [App\Http\Controllers\AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
    Route::post('/register', [App\Http\Controllers\AdminAuthController::class, 'register'])->name('admin.register.submit');
    Route::post('/logout', [App\Http\Controllers\AdminAuthController::class, 'logout'])->name('admin.logout');

    // Password Reset (Admin)
    Route::get('password/reset', [App\Http\Controllers\AdminAuth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
    Route::post('password/email', [App\Http\Controllers\AdminAuth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('password/reset/{token}', [App\Http\Controllers\AdminAuth\ResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('password/reset', [App\Http\Controllers\AdminAuth\ResetPasswordController::class, 'reset'])->name('admin.password.update');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/financial-details', [AdminController::class, 'financialDetails'])->name('admin.financial.details');
    
    // Routes Management
    Route::get('/routes', [AdminController::class, 'routes'])->name('admin.routes');
    Route::get('/routes/create', [AdminController::class, 'createRoute'])->name('admin.routes.create');
    Route::post('/routes', [AdminController::class, 'storeRoute'])->name('admin.routes.store');
    Route::get('/routes/{id}/edit', [AdminController::class, 'editRoute'])->name('admin.routes.edit');
    Route::put('/routes/{id}', [AdminController::class, 'updateRoute'])->name('admin.routes.update');
    Route::delete('/routes/{id}', [AdminController::class, 'deleteRoute'])->name('admin.routes.delete');
    
    // Drivers Management
    Route::get('/drivers', [AdminController::class, 'drivers'])->name('admin.drivers');
    Route::get('/drivers/{id}', [AdminController::class, 'driverDetails'])->name('admin.drivers.show');
    Route::post('/drivers/{id}/verify', [AdminController::class, 'verifyDriver'])->name('admin.drivers.verify');
    Route::post('/drivers/{id}/toggle', [AdminController::class, 'toggleDriverStatus'])->name('admin.drivers.toggle');
    
    // Trips Management
    Route::get('/trips', [AdminController::class, 'trips'])->name('admin.trips');
    Route::get('/trips/create', [AdminController::class, 'createTrip'])->name('admin.trips.create');
    Route::post('/trips', [AdminController::class, 'storeTrip'])->name('admin.trips.store');
    Route::get('/trips/{id}', [AdminController::class, 'tripDetails'])->name('admin.trips.show');
    Route::get('/trips/{id}/edit', [AdminController::class, 'editTrip'])->name('admin.trips.edit');
    Route::put('/trips/{id}', [AdminController::class, 'updateTrip'])->name('admin.trips.update');
    Route::post('/trips/{id}/cancel', [AdminController::class, 'cancelTrip'])->name('admin.trips.cancel');
    Route::delete('/trips/{id}', [AdminController::class, 'deleteTrip'])->name('admin.trips.delete');
    
    // Bookings Management
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::get('/bookings/search', [AdminController::class, 'adminBookingSearch'])->name('admin.bookings.search');
    Route::get('/bookings/create/{tripId}', [AdminController::class, 'adminBookingForm'])->name('admin.bookings.create');
    Route::post('/bookings', [AdminController::class, 'adminBooking'])->name('admin.bookings.store');
    Route::get('/bookings/{id}', [AdminController::class, 'bookingDetails'])->name('admin.bookings.show');
    Route::get('/bookings/{booking}/payment', [AdminController::class, 'adminPaymentProcess'])->name('admin.payment.process');
    Route::post('/bookings/{booking}/payment/complete', [AdminController::class, 'adminPaymentComplete'])->name('admin.payment.complete');
    Route::post('/bookings/{booking}/payment/timeout', [AdminController::class, 'adminPaymentTimeout'])->name('admin.payment.timeout');
    Route::get('/bookings/{booking}/receipt', [AdminController::class, 'adminBookingReceipt'])->name('admin.booking.receipt');
    
    // Payouts Management
    Route::get('/payouts', [AdminController::class, 'payouts'])->name('admin.payouts');
    Route::post('/payouts/{id}/process', [AdminController::class, 'processPayout'])->name('admin.payouts.process');
    Route::post('/payouts/process-all', [AdminController::class, 'processAllPayouts'])->name('admin.payouts.processAll');
    
    // Complaints Management
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('admin.complaints');
    Route::post('/complaints/{id}/resolve', [AdminController::class, 'resolveComplaint'])->name('admin.complaints.resolve');
    Route::post('/complaints/{id}/respond', [AdminController::class, 'respondComplaint'])->name('admin.complaints.respond');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/reports/export/trips', [AdminController::class, 'exportTrips'])->name('admin.export.trips');
    Route::get('/reports/export/bookings', [AdminController::class, 'exportBookings'])->name('admin.export.bookings');
    Route::get('/reports/export/payments', [AdminController::class, 'exportPayments'])->name('admin.export.payments');
    Route::get('/reports/export/drivers', [AdminController::class, 'exportDrivers'])->name('admin.export.drivers');
    Route::get('/reports/export/payouts', [AdminController::class, 'exportPayouts'])->name('admin.export.payouts');
    Route::get('/reports/search/trips', [AdminController::class, 'searchTrips'])->name('admin.reports.search.trips');
    Route::get('/reports/search/bookings', [AdminController::class, 'searchBookings'])->name('admin.reports.search.bookings');
    Route::get('/reports/search/payments', [AdminController::class, 'searchPayments'])->name('admin.reports.search.payments');
    Route::get('/reports/search/drivers', [AdminController::class, 'searchDrivers'])->name('admin.reports.search.drivers');
    Route::get('/reports/search/payouts', [AdminController::class, 'searchPayouts'])->name('admin.reports.search.payouts');
    Route::get('/reports/search/refunds', [AdminController::class, 'searchRefunds'])->name('admin.reports.search.refunds');
    Route::post('/reports/update/booking/{id}', [AdminController::class, 'updateBookingStatus'])->name('admin.reports.update.booking');
    Route::post('/reports/update/payment/{id}', [AdminController::class, 'updatePaymentStatus'])->name('admin.reports.update.payment');
    Route::post('/reports/update/refund/{id}', [AdminController::class, 'updateRefundStatus'])->name('admin.reports.update.refund');
    Route::post('/reports/update/payout/{id}', [AdminController::class, 'updatePayoutStatus'])->name('admin.reports.update.payout');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    
    // Users Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
});

// Admin Route for Payout Settings
Route::post('/admin/settings/payout', [AdminController::class, 'updatePayoutSettings'])->name('admin.settings.payout')->middleware('auth');
