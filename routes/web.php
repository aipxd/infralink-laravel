<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FacilityController;
use Illuminate\Support\Facades\Route;

// Redirect root to facilities catalog
Route::get('/', function () {
    return redirect()->route('facilities.index');
});

// Authentication routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Facilities (Catalog & Calendar)
    Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
    Route::get('/facilities/{id}', [FacilityController::class, 'show'])->name('facilities.show');
    Route::get('/facilities/{id}/calendar', [FacilityController::class, 'calendarEvents'])->name('facilities.calendar');

    // Bookings (Requester)
    Route::get('/facilities/{id}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/mine', [BookingController::class, 'mine'])->name('bookings.mine');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/bookings/attachment/{id}/download', [BookingController::class, 'downloadAttachment'])->name('bookings.attachment.download');

    // Approver & Admin Routes
    Route::middleware('role:APPROVER,ADMIN')->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/{id}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });

    // Admin Only Routes
    Route::middleware('role:ADMIN')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Facilities Management
        Route::get('/facilities', [AdminController::class, 'facilities'])->name('facilities.index');
        Route::get('/facilities/create', [AdminController::class, 'createFacility'])->name('facilities.create');
        Route::post('/facilities', [AdminController::class, 'storeFacility'])->name('facilities.store');
        Route::get('/facilities/{id}/edit', [AdminController::class, 'editFacility'])->name('facilities.edit');
        Route::put('/facilities/{id}', [AdminController::class, 'updateFacility'])->name('facilities.update');
        Route::post('/facilities/{id}/toggle-status', [AdminController::class, 'toggleFacilityStatus'])->name('facilities.toggle-status');
        Route::delete('/facilities/{id}', [AdminController::class, 'destroyFacility'])->name('facilities.destroy');

        // Categories Management
        Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

        // Blocked Slots Management
        Route::get('/blocked-slots', [AdminController::class, 'blockedSlots'])->name('blocked-slots.index');
        Route::post('/blocked-slots', [AdminController::class, 'storeBlockedSlot'])->name('blocked-slots.store');
        Route::delete('/blocked-slots/{id}', [AdminController::class, 'destroyBlockedSlot'])->name('blocked-slots.destroy');

        // Users Management
        Route::get('/users', [AdminController::class, 'users'])->name('users.index');
        Route::post('/users/{id}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');
        Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('users.toggle-status');
    });
});
