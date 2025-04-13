<?php

namespace App\Http\Controllers\Auth;

use App\Auth\PasswordlessUserGuard;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordlessLoginNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordlessLoginController extends Controller
{
    public function sendLoginLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = $request->input('email');

        /** @var PasswordlessUserGuard $guard */
        $guard = Auth::guard('passwordless');
        $loginLink = $guard->attemptPasswordlessLogin($email);

        if (! $loginLink) {
            return back()->withErrors([
                'email' => 'This email is not registered or cannot use passwordless login.',
            ]);
        }

        // Send the email with the login link
        // You'll need to create this notification
        $user = User::where('email', $email)->first();
        $user->notify(new PasswordlessLoginNotification($loginLink));

        return back()->with('status', 'We have sent a login link to your email address!');
    }

    public function verifyLogin(Request $request)
    {
        $email = $request->input('email');
        $token = $request->input('token');

        /** @var PasswordlessUserGuard $guard */
        $guard = Auth::guard('web');

        if (! $guard->validatePasswordlessLogin($email, $token)) {
            return redirect()->route('login')
                ->withErrors(['email' => 'The login link is invalid or has expired.']);
        }

        return redirect()->intended(route('dashboard'));
    }
}
