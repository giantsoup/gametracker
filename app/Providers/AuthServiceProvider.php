<?php

namespace App\Providers;

use App\Auth\PasswordlessUserGuard;
use App\Models\GamePoint;
use App\Models\User;
use App\Policies\GamePointPolicy;
use Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        GamePoint::class => GamePointPolicy::class,
        Game::class => GamePolicy::class,
    ];

    public function boot(): void
    {
        Auth::extend('passwordless', function ($app, $name, array $config) {
            return new PasswordlessUserGuard(
                Auth::createUserProvider($config['provider'] ?? null),
                $app->make('request')
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

        // Allow regular users to create resources (except users which are handled by policies)
        Gate::define('create', function (User $user) {
            return true; // Both admins and users can create
        });

        // Allow regular users to update resources (except users which are handled by policies)
        Gate::define('update', function (User $user) {
            return true; // Both admins and users can update
        });

        // Allow regular users to delete resources (except users which are handled by policies)
        Gate::define('delete', function (User $user) {
            return true; // Both admins and users can delete
        });
    }
}
