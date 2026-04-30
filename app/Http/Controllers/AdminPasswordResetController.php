<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRules;

class AdminPasswordResetController extends Controller
{
    // ── Step 1: Forgot password form ─────────────────────────────

    public function showForgotForm()
    {
        return view('Admin.forgot-password');
    }

    // ── Step 2: Email submit → reset link bhejo ───────────────────

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ], [
            // Deliberately vague — don't confirm whether email exists
            'email.exists' => 'If this email is registered, a reset link will be sent.',
        ]);

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(
                'success',
                'Password reset instructions have been sent to your email address. Link expires in 15 minutes.'
            );
        }

        // Throttled or other error —
        return back()->with(
            'success',
            'If this email is registered, a reset link will be sent.'
        );
    }

    // ── Step 3: Reset form (it come on email link ) ──────────────

    public function showResetForm(Request $request, string $token)
    {
        return view('Admin.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // ── Step 4: new password save ──────────────────────────

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                PasswordRules::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Admin $admin, string $password) {
                $admin->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($admin));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')
                ->with('success', 'Password reset successfully. You can now log in with your new password.')
            : back()->withErrors(['email' => 'This reset link is invalid or has expired. Please request a new one.']);
    }
}
