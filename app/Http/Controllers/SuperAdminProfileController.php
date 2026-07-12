<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminProfileController extends Controller
{
    public function edit()
    {
        $superadmin = Auth::guard('superadmin')->user();

        return view('SuperAdmin.profile', compact('superadmin'));
    }

    public function update(Request $request)
    {
        $superadmin = Auth::guard('superadmin')->user();

        $data = $request->validate([
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($superadmin->id)],
            'current_password' => ['required'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ], [
            'current_password.required' => 'Enter your current password to confirm changes.',
        ]);

        // Require the current password for ANY change here — this account
        // controls the entire platform, so email/password edits shouldn't
        // be possible just from an already-open session.
        if (! Hash::check($data['current_password'], $superadmin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $superadmin->email = $data['email'];

        if (! empty($data['password'])) {
            $superadmin->password = Hash::make($data['password']);
        }

        $superadmin->save();

        return back()->with('success', 'Your profile has been updated.');
    }
}
