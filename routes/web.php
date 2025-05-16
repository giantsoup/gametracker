<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\EventController;
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

    // Event Routes
    Route::get('events', [App\Http\Controllers\EventController::class, 'index'])->name('events.index');
    Route::get('events/{event}', [App\Http\Controllers\EventController::class, 'show'])->name('events.show');

    Route::middleware(['admin'])->group(function () {
        Route::get('admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::get('admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
        Route::get('admin/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
        Route::get('admin/logs', [AdminController::class, 'logs'])->name('admin.logs');

        // User Management Routes
        Route::resource('admin/users', UserController::class, [
            'as' => 'admin',
        ]);

        // Event Management Routes
        Route::resource('admin/events', EventController::class, [
            'as' => 'admin',
        ]);
    });
});

require __DIR__.'/auth.php';
