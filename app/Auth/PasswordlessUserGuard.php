<?php

namespace App\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class PasswordlessUserGuard extends SessionGuard
{
    public function __construct(UserProvider $provider, Request $request)
    {
        parent::__construct('web', $provider, session(), $request);
    }

    public function attemptPasswordlessLogin(string $email): ?string
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return null;
        }

        // Only allow passwordless login for regular users
        if ($user->role !== UserRole::USER) {
            return null;
        }

        // Generate a signed URL for email verification
        $token = Str::random(60);

        // Store the token in the session
        session()->put('passwordless_login_token:'.$email, $token);

        // Generate a signed URL for verification
        return URL::temporarySignedRoute(
            'verification.passwordless',
            now()->addMinutes(30),
            [
                'email' => $email,
                'token' => $token,
            ]
        );
    }

    public function validatePasswordlessLogin(string $email, string $token): bool
    {
        $storedToken = session()->pull('passwordless_login_token:'.$email);

        if (! $storedToken || $storedToken !== $token) {
            return false;
        }

        $user = User::where('email', $email)->first();

        if (! $user || $user->role !== UserRole::USER) {
            return false;
        }

        // Log the user in
        $this->login($user);

        // Mark email as verified if not already
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return true;
    }
}
