<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    use ApiResponse;

    // ─────────────────────────────────────
    // Register
    // ─────────────────────────────────────
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
                'password_confirmation' => 'required',
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Token create
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Registration successful!', 201);
    }

    // ─────────────────────────────────────
    // Login
    // ─────────────────────────────────────
    public function login(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        }

        // Verify credentials directly (no Auth::attempt()) — this API is
        // stateless (Sanctum tokens), so starting a session guard would
        // just write a useless row to the sessions table on every login.
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Invalid email or password.', 401);
        }

        if (! $user->is_active) {
            return $this->error('Your account has been suspended. Please contact support.', 403);
        }

        // old tokens delete (single device login)
        $user->tokens()->delete();

        // new token
        $token = $user->createToken('api-token')->plainTextToken;

        return $this->success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login successful!');
    }

    // ─────────────────────────────────────
    // Logout
    // ─────────────────────────────────────
    public function logout(Request $request): JsonResponse
    {
        // Delete current token
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully.');
    }

    // ─────────────────────────────────────
    // Me — Current user info
    // ─────────────────────────────────────
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('profile');

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_complete' => $user->profile ? true : false,
        ]);
    }
}
