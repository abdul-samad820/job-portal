<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\SessionInvalidator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;

class PasswordResetController extends Controller
{
    // ─────────────────────────────────────────
    // Step 1: Show Forgot Password Form
    // ─────────────────────────────────────────
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // ─────────────────────────────────────────
    // Step 2: Submit Email → Send Reset Link
    // ─────────────────────────────────────────
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return back()->with(
            'success',
            'If this email is registered, a password reset link will be sent.'
        );
    }

    // ─────────────────────────────────────────
    // Step 3: Show Reset Password Form
    // ─────────────────────────────────────────
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // ─────────────────────────────────────────
    // Step 4: Save New Password
    // ─────────────────────────────────────────
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRules::min(8)->mixedCase()->numbers()],
        ], [
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters long.',
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Fire Laravel event (invalidates sessions, etc.)
                event(new PasswordReset($user));

                // Force logout on every other device — an attacker with a
                // stolen session shouldn't stay logged in after the real
                // owner resets their password.
                SessionInvalidator::invalidateForUser($user->id, 'user');
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('user.login')
                ->with('success', 'Your password has been reset successfully. You can now log in.')
            : back()->withErrors([
                'email' => 'The reset link is invalid or has expired. Please request a new one.',
            ]);
    }
}
