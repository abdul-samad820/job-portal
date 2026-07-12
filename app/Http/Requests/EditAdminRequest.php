<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class EditAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('superadmin')->check()
            && auth('superadmin')->user()->role === 'super_admin';
    }

    public function rules(): array
    {
        $adminId = $this->route('id');

        return [
            'company_name' => 'required|string|max:255',
            'contact_number' => ['nullable', 'digits_between:10,12', Rule::unique('admins', 'contact_number')->ignore($adminId)],
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1080',
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($adminId)],
            // Password is optional on edit — only validated/updated if provided.
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'email.unique' => 'Another admin already uses this email.',
            'password.confirmed' => 'Passwords do not match.',
            'contact_number.unique' => 'This contact number is already registered.',
        ];
    }
}
