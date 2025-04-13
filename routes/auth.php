<?php

use App\Http\Controllers\Auth\PasswordlessLoginController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    Volt::route('login', 'auth.login')
        ->name('login');

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('password.reset');

});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('password.confirm');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');

// Add these routes to your existing guest middleware group
Route::middleware('guest')->group(function () {
    // Keep your existing routes

    // Add new passwordless routes
    Route::get('passwordless-login', [PasswordlessLoginController::class, 'showLoginForm'])
        ->name('passwordless.login');

    Route::post('passwordless-login', [PasswordlessLoginController::class, 'sendLoginLink'])
        ->name('passwordless.send');
});

// Add this route outside any middleware group
Route::get('verify-login/{email}/{token}', [PasswordlessLoginController::class, 'verifyLogin'])
    ->middleware('signed')
    ->name('verification.passwordless');
