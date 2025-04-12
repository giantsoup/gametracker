<?php

namespace App\Providers;

use App\Auth\PasswordlessUserGuard;
use App\Models\User;
use Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Auth::extend('passwordless', function ($app, $name, array $config) {
            return new PasswordlessUserGuard(
                Auth::createUserProvider($config['provider'] ?? null),
                $app['request']
            );
        });

        // Define a basic gate for admin access
        Gate::define('admin', function (User $user) {
            return $user->isAdmin();
        });

        // Define a read-only gate for regular users
        Gate::define('view', function (User $user) {
            return true; // Both admins and users can view
        });

        // Example of a specific action only admins can do
        Gate::define('create', function (User $user) {
            return $user->isAdmin();
        });

        // Example of a specific action only admins can do
        Gate::define('update', function (User $user) {
            return $user->isAdmin();
        });

        // Example of a specific action only admins can do
        Gate::define('delete', function (User $user) {
            return $user->isAdmin();
        });

        // You can add more specific gates as needed
    }
}
