<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::middleware(['admin'])->group(function () {
        Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::get('admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
        Route::get('admin/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
        Route::get('admin/logs', [AdminController::class, 'logs'])->name('admin.logs');

        // User Management Routes
        Route::resource('admin/users', UserController::class, [
            'as' => 'admin',
        ]);
        // Livewire Route for User Management
        Volt::route('admin/users-management', 'admin.user-management')
            ->name('admin.users-management');
    });
});

require __DIR__.'/auth.php';
